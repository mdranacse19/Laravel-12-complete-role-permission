<?php

namespace App\Traits;

use Closure;

trait ChecksUniqueAcrossModels
{
    protected function checkIfValueExists(string $attribute, mixed $value, Closure $fail): void
    {
        $array = array_filter($this->modelsToCheck, fn($model) => $model != $this->ignore);
        foreach ($array as $modelClass) {
            $query = $modelClass::query();

            if ($this->excludeModel === $modelClass && $this->excludeId) {
                $query->where($this->excludeColumn, '!=', $this->excludeId);
            }

            if ($query->where($attribute, $value)->exists()) {
                $fail('The :attribute has already been taken in ' . strtolower(class_basename($modelClass)) . '.');
                break;
            }
        }
    }
}
