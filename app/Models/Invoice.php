<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    const STATUS_PAID = 'مدفوعة';
    const STATUS_PARTIAL_PAID = 'مدفوعة جزئيا';
    const STATUS_NOT_PAID = 'غير مدفوعة';
    const STATUS_PAID_VALUE= 1;
    CONST STATUS_NOT_PAID_VALUE = 2;
    const STATUS_PARTIAL_PAID_VALUE= 3;
    protected $guarded= [ 'id', 'created_at', 'updated_at', 'deleted_at'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

}
