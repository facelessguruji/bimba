<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class WithdrawMethod extends Model
{
    use GlobalStatus;
    protected $casts = [
        'user_data' => 'object'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function showData(): Attribute
    {
        return new Attribute(function(){
            $data = json_decode(json_encode(@$this->form->form_data), true);
            $form = $data ?? [];
            return $form;
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }
}
