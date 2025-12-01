<?php

namespace App\Enums;

use App\Traits\Enum\Arrayable;
use App\Traits\Enum\Invokable;

/**
 * Resource status types.
 */
enum Status: string
{
    use Arrayable, Invokable;

    /**
     * Resource status is pending or on review stage.
     */
    case PENDING = 'Pending';

    /**
     * Resource status is active.
     */
    case ACTIVE = 'Active';
    
    /**
     * Resource status is approved.
     */
    case APPROVED = 'Approved';

    /**
     * Resource status is blocked or declined.
     */
    case DECLINED = 'Declined';

    /**
     * Check if a particular resource is active.
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Check if a particular resource is pending.
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if a particular resource is approved.
     */
    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Check if a particular resource is declined.
     */
    public function isDeclined(): bool
    {
        return $this === self::DECLINED;
    }

    /**
     * Get resource tag by status.
     */
    public function tag(): string
    {
        return match ($this) {
            self::ACTIVE => 'info',
            self::PENDING => 'warn',
            self::APPROVED => 'success',
            self::DECLINED => 'danger',
        };
    }

    /**
     * Get resource status label.
     */
    public function label(string $lang = 'en'): string
    {
        return match ($this) {
            self::ACTIVE => $lang == 'bn' ? 'সক্রিয়' : 'Active',
            self::APPROVED => $lang == 'bn' ? 'অনুমোদিত' : 'Approved',
            self::PENDING => $lang == 'bn' ? 'নিষ্ক্রিয়' : 'Inactive',
            self::DECLINED => $lang == 'bn' ? 'প্রত্যাখ্যাত' : 'Declined',
        };
    }

    /**
     * Get resource status as options.
     */
    public static function options(): array
    {
        return [
            [
                'value' => self::APPROVED,
                'label' => 'Approved',
            ],
            [
                'value' => self::ACTIVE,
                'label' => 'Active',
            ],
            [
                'value' => self::PENDING,
                'label' => 'Inactive',
            ],
            [
                'value' => self::DECLINED,
                'label' => 'Declined',
            ],
        ];
    }

    /**
     * Get dropdown options as array.
     */
    public static function dropdownOptions(): array
    {
        return [
            [
                'value' => self::APPROVED,
                'label' => 'Approved',
            ],
            [
                'value' => self::ACTIVE,
                'option' => 'Active',
            ],
            [
                'value' => self::PENDING,
                'option' => 'Inactive',
            ],
        ];
    }

    /**
     * Get resources HTML badge by status.
     */
    public function badge(): string
    {
        return sprintf('<span class="badge badge-%s">%s</span>', $this->tag(), $this->label());
    }
}