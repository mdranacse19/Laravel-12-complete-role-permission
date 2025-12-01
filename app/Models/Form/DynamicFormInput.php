<?php

namespace App\Models\Form;

use App\Models\Setup\FormInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicFormInput extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'dynamic_form_id',
        'form_input_id',
        'label',
        'placeholder',
        'options',
        'required',
        'has_action',
        'sort',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'has_action' => 'boolean',
        'sort' => 'integer',
    ];

    public function input(): BelongsTo
    {
        return $this->belongsTo(FormInput::class, 'form_input_id');
    }
}
