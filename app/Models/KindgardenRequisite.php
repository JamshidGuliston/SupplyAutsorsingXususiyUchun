<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KindgardenRequisite extends Model
{
    use HasFactory;

    protected $fillable = [
        'kindgarden_id',
        'director_name',
        'director_phone',
        'reception_phone',
        'address',
        'inn',
        'bank_account',
        'mfo',
        'treasury_account',
        'bank',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function kindgarden()
    {
        return $this->belongsTo(Kindgarden::class);
    }
}
