<?php

namespace App\Traits\Enum;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Arrayable
{
    /**
     * Returns case names as array.
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Returns case values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Returns array where keys are case values and values are case names.
     */
    public static function toArray(): array
    {
        return array_combine(self::values(), Arr::map(self::names(), function (string $value, int $key) {
            return ucwords(Str::lower($value));
        }));
    }

    /**
     * Returns array where keys are case values and values are case names.
     */
    public static function options(): array
    {
        $options = Arr::map(self::cases(), function ($case, $key) {
            return [
                'option' => $case->label(),
                'value' => $case->value,
            ];
        });

        usort($options, function ($a, $b) {
            return $a['option'] <=> $b['option'];
        });

        return $options;
    }

    /**
     * Returns array where keys are case values and values are case names.
     */
    public static function labelValuePair(): array
    {
        return Arr::map(self::cases(), function ($case, $key) {
            return [
                'label' => method_exists($case, 'label') ? $case->label() : ucfirst($case->value),
                'value' => $case->value,
            ];
        });
    }
}
