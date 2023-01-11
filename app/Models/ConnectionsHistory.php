<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectionsHistory extends Model
{
    use HasFactory;
    protected $table = "connections_history";

    CONST TYPE_REFILL = "refill";
    CONST TYPE_EXPENSE = "expense";

    protected $fillable = [
        'amount',
        'cv_id',
        'type',
        'company_id',
        'user_id'
    ];
}
