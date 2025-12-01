<?php

namespace App\Enums;

use App\Traits\Enum\Arrayable;
use App\Traits\Enum\Invokable;

enum FormType: string
{
    use Arrayable, Invokable;

    /**
     * Resource type is assessment.
     */
    case ASSESSMENT = 'Assessment';

    /**
     * Resource type is monitoring.
     */
    case MONITORING = 'Monitoring';

    /**
     * Check if resource type is assessment.
     */
    public function isAssessment(): bool
    {
        return $this === self::ASSESSMENT;
    }

    /**
     * Check if resource type is monitoring.
     */
    public function isMonitoring(): bool
    {
        return $this === self::MONITORING;
    }


    /**
     * Get resource tag by type.
     */
    public function tag(): string
    {
        return match ($this) {
            self::ASSESSMENT => 'contrast',
            self::MONITORING => 'info',
        };
    }

    /**
     * Get resources type label.
     */
    public function label(string $lang = 'en'): string
    {
        return match ($this) {
            self::ASSESSMENT => $lang == 'bn' ? 'মূল্যায়ন' : 'Assessment',
            self::MONITORING => $lang == 'bn' ? 'পর্যবেক্ষণ' : 'Monitoring',
        };
    }

    /**
     * Get resources HTML badge by type.
     */
    public function badge(): string
    {
        return sprintf('<span class="badge badge-%s">%s</span>', $this->tag(), $this->label());
    }
}
