<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'receipt_number',
        'purchase_date',
        'receipt_image',
        'accept_terms',
        'accept_marketing',
    ];
}

