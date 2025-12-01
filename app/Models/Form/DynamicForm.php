<?php

namespace App\Models\Form;

use App\Enums\FormType;
use App\Enums\Status;
use App\Models\Setup\FormInput;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DynamicForm extends Model
{
    use HasFactory, HasSlug;

    protected $primaryKey = 'id';

    protected $fillable = [
        'type',
        'form_id',
        'name',
        'slug',
        'is_active',
    ];

    public $timestamps = false;

    protected string $slugFrom = 'name';

    protected string $slugColumn = 'slug';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => FormType::class,
    ];

    public function inputs(): BelongsToMany
    {
        return $this->belongsToMany(
            FormInput::class,
            'dynamic_form_inputs',
            'dynamic_form_id',
            'form_input_id'
        )->withPivot('label', 'placeholder', 'options', 'required', 'has_action', 'id')
            ->orderBy('sort');
    }
}
