<?php

namespace App\Rules;

use App\Models\Profile\Admin;
use App\Models\Profile\Association;
use App\Models\Profile\Stakeholder;
use App\Models\User;
use App\Traits\ChecksUniqueAcrossModels;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueEmailAcrossProfiles implements ValidationRule
{
    use ChecksUniqueAcrossModels;

    protected ?string $excludeModel;
    protected int|string|null $excludeId;
    protected string $excludeColumn;
    protected string $ignore;

    protected ValidEmailRule $formatValidator;

    protected array $modelsToCheck = [
        Admin::class,
        User::class,
    ];

    public function __construct(
        ?string         $excludeModel = null,
        int|string|null $excludeId = null,
        string          $excludeColumn = 'id',
        string          $ignore = ''
    )
    {
        $this->excludeModel = $excludeModel;
        $this->excludeId = $excludeId;
        $this->excludeColumn = $excludeColumn;
        $this->ignore = $ignore;

        $this->formatValidator = new ValidEmailRule;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attribute = $attribute === 'emailAddress' ? 'email' : $attribute;
        $this->formatValidator->validate($attribute, $value, $fail);

        $this->checkIfValueExists($attribute, $value, $fail);
    }
}
