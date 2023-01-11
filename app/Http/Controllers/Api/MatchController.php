<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchCardResource;
use App\Http\Resources\MatchDetailedResource;
use App\Mail\InterviewScheduled;
use App\Models\Matching;
use App\Models\MatchNotes;
use App\Notifications\MatchDeclined;
use App\Notifications\OfferAccepted;
use App\Services\MatchService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class MatchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $statusLabel = [
            Matching::STATUS_MATCHED => 'match',
            Matching::STATUS_CANDIDATE_INTERESTED => 'review',
            Matching::STATUS_COMPANY_INTERESTED => 'mutual',
            Matching::STATUS_INTERVIEW => 'interview',
            Matching::STATUS_OFFER => 'offer',
            Matching::STATUS_COMPLETE => 'offer'
        ];
        if ($request->has('company_id')) {
            $matches = Matching::where('company_id', $request->get('company_id'))
                ->where('status', '!=', Matching::STATUS_MATCHED);
            $statusLabel[Matching::STATUS_COMPANY_POSTPONE] = 'postponed';
        } else {
            $matches = Matching::where('user_id', auth()->user()->id);
            $statusLabel[Matching::STATUS_COMPANY_POSTPONE] = 'review';
            $statusLabel[Matching::STATUS_CANDIDATE_POSTPONE] = 'postponed';
        }

        $matches = $matches->with(['cv','vacancy.skills','company','candidate.profile'])->get();

        $matches = MatchCardResource::collection($matches)->resolve();


        $out = [
            'match' => [],
            'review' => [],
            'mutual' => [],
            'interview' => [],
            'offer' => [],
            'postponed' => []
        ];

        foreach ($matches as $match) {
            if (isset($statusLabel[$match['status']])) {
                $out[$statusLabel[$match['status']]][] = $match;
            }
        }

        return response()->json(
            getResponseStructure($out)
        );
    }

    public function archiveList(Request $request): JsonResponse
    {
        if ($request->has('company_id')) {
            $matches = Matching::where('company_id', $request->get('company_id'));
        } else {
            $matches = Matching::where('user_id', auth()->user()->id);
        }

        $matches = $matches->whereIn('status', [Matching::STATUS_DECLINED_BY_COMPANY, Matching::STATUS_DECLINED_BY_CANDIDATE])
            ->with(['cv','vacancy','company','candidate.profile'])->get();

        return response()->json([
            'matches' => $matches
        ]);
    }

    public function show($id): JsonResponse
    {
        $user = auth()->user();
        $match = Matching::find($id);
        if (MatchService::checkPermissionToView($match, $user)) {
            $match->load([
                'cv.skills.technology',
                'vacancy',
                'company',
                'candidate.profile.language',
                'candidate.profile.additionalLanguages',
                'interviews',
                'notes'
            ]);

            return response()->json(
                getResponseStructure(MatchDetailedResource::make($match))
            );
        }

        return response()->json(
            getResponseStructure([],false),
            403
        );
    }

    public function changeStatus(Request $request): JsonResponse
    {
        $data = $request->all();
        $match = Matching::find($data['id']);
        $match->status = $data['status'];
        $match->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function submitInterview(Request $request): JsonResponse
    {
        $data = $request->all();
        $interviewData = [
            'interview_date' => Carbon::parse($data['interview_date']),
            'interview_time' => $data['interview_time']
        ];
        $match = Matching::find($data['id']);
        if ($match->interviews) {
            $match->interviews()->update(['is_current' => 0]);
        }
        $match->interviews()->create($interviewData);
        $match->status = Matching::STATUS_INTERVIEW;
        $match->save();

        $user = $match->candidate;

        Mail::to($user->email)->send(new InterviewScheduled($match, $interviewData));

        return response()->json([
            'success' => true
        ]);
    }

    public function offer(Request $request): JsonResponse
    {
        $data = $request->all();
        $match = Matching::find($data['id']);
        $match->status = Matching::STATUS_OFFER;
        $match->save();

        //todo Notification about offer?

        return response()->json([
            'success' => true
        ]);
    }

    public function accept(Request $request): JsonResponse
    {
        $data = $request->all();
        $match = Matching::find($data['id']);
        $match->load('vacancy.creator');
        $match->status = Matching::STATUS_COMPLETE;
        $match->save();

        $match->vacancy->creator->notify(new OfferAccepted($match));

        return response()->json([
            'success' => true
        ]);
    }

    public function decline(Request $request): JsonResponse
    {
        $data = $request->all();
        $match = Matching::find($data['id'])->load(['candidate', 'company']);
        $match->decline_reason = $data['reason'];
        if ($data['type'] == 'cv') {
            $match->status = Matching::STATUS_DECLINED_BY_COMPANY;
        } else {
            $match->status = Matching::STATUS_DECLINED_BY_CANDIDATE;
        }
        $match->save();

        Notification::send($match->candidate, new MatchDeclined($data['type'], $match));

        return response()->json([
            'success' => true
        ]);
    }

    public function addNote(Request $request) {
        $data = $request->validate([
            'message' => "required",
            'match_id' => "required|numeric"
        ]);

        if($data) {
            $data['user_id'] = auth()->user()->id;
            MatchNotes::create($data);
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function editNote($id, $action = 'remove'): JsonResponse
    {
        $note = MatchNotes::find($id);
        if($note) {
            if($action === 'remove') {
                $note->is_deleted = 1;
                $note->save();
            }
            else if($action === 'restore') {
                $note->is_deleted = 0;
                $note->save();
            }
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }
}
