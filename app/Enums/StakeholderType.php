<?php

namespace App\Enums;

use App\Traits\Enum\Arrayable;
use App\Traits\Enum\Invokable;

enum StakeholderType: string
{
    use Arrayable, Invokable;

    /**
     * Partner Type is governmental.
     */
    case GOVERNMENT = 'Government';

    /**
     * Partner Type is autonomous.
     */
    case AUTONOMOUS = 'Autonomous';

    /**
     * Partner Type is ngo.
     */
    case NGO = 'NGO';

    /**
     * Partner Type is Private Sector.
     */
    case PRIVATE = 'Private Sector';

    /**
     * Partner Type is none of the above.
     */
    case OTHERS = 'Other';

    /**
     * Check if a particular partner type is governmental.
     */
    public function isGovernment(): bool
    {
        return $this === self::GOVERNMENT;
    }

    /**
     * Check if a particular partner type is ngo.
     */
    public function isNgo(): bool
    {
        return $this === self::NGO;
    }

    /**
     * Check if a particular partner type is autonomous.
     */
    public function isAutonomous(): bool
    {
        return $this === self::AUTONOMOUS;
    }

    public function isPrivateSector(): bool
    {
        return $this === self::PRIVATE;
    }

    /**
     * Check if a particular partner type is none of the above.
     */
    public function isOther(): bool
    {
        return $this === self::OTHERS;
    }

    /**
     * Get partner type tag by type.
     */
    public function tag(): string
    {
        return match ($this) {
            self::GOVERNMENT => 'primary',
            self::NGO => 'info',
            self::AUTONOMOUS => 'warning',
            self::PRIVATE => 'success',
            self::OTHERS => 'secondary',
        };
    }

    /**
     * Get partner type label.
     */
    public function label(string $language = 'en'): string
    {
        return match ($this) {
            self::GOVERNMENT => $language === 'bn' ? 'সরকারি' : 'Government',
            self::NGO => $language === 'bn' ? 'এনজিও' : 'NGO',
            self::AUTONOMOUS => $language === 'bn' ? 'স্বায়ত্তশাসিত' : 'Autonomous',
            self::PRIVATE => $language === 'bn' ? 'বেসরকারি খাত' : 'Private Sector',
            self::OTHERS => $language === 'bn' ? 'অন্যান্য' : 'Others',
        };
    }

    /**
     * Get resource type as options.
     */
    public static function options(): array
    {
        return [
            [
                'value' => self::GOVERNMENT,
                'label' => 'Government',
            ],
            [
                'value' => self::NGO,
                'label' => 'NGO',
            ],
            [
                'value' => self::AUTONOMOUS,
                'label' => 'AUTONOMOUS',
            ],
            [
                'value' => self::PRIVATE,
                'label' => 'Private Sector',
            ],
            [
                'value' => self::OTHERS,
                'label' => 'Others',
            ],
        ];
    }

    /**
     * Get HTML badge by partner type.
     */
    public function badge(): string
    {
        return sprintf('<span class="badge badge-%s">%s</span>', $this->tag(), $this->label());
    }
}
