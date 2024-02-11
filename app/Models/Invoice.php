<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    const STATUS_PAID = 'مدفوعة';
    const STATUS_NOT_PAID = 'غير مدفوعة';
    const STATUS_PAID_VALUE= 1;
    CONST STATUS_NOT_PAID_VALUE =2;
    protected $guarded= [ 'id', 'created_at', 'updated_at', 'deleted_at'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

}
