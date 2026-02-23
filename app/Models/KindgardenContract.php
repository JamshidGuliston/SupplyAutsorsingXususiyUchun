<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KindgardenContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'kindgarden_id',
        'contract_number',
        'contract_date',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'start_date'    => 'date',
        'end_date'      => 'date',
    ];

    public function kindgarden()
    {
        return $this->belongsTo(Kindgarden::class);
    }
}
