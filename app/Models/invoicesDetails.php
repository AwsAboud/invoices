<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicesDetails extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_id','product','section','invoice_number',
        'status','value_status','payment_date','note','created_by',
    ];
}
