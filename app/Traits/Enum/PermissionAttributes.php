<?php

namespace App\Traits\Enum;

use App\Enums\Attributes\Abilities;
use App\Enums\Attributes\Description;
use App\Enums\Attributes\Group;
use App\Enums\Attributes\Module;
use BackedEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClassConstant;

trait PermissionAttributes
{
    /**
     * Retrieves all the permission names.
     */
    public static function all(): array
    {
        return collect(self::cases())
            ->map(function ($enum) {
                return count(self::abilities($enum)) ? Arr::map(self::abilities($enum), function (string $ability, int $key) use ($enum) {
                    return $enum->value.'_'.$ability;
                }) : $enum->value.'_access';
            })->flatten()->toArray();
    }

    /**
     * Returns all the permission names as key and all the details of the permission as value.
     */
    public static function withDetails(): array
    {
        $permissions = [];

        foreach (self::cases() as $enum) {
            foreach (self::abilities($enum) as $ability) {
                $permissions[$enum->value.'_'.$ability] = [
                    'module' => self::module($enum),
                    'name' => $enum->name,
                    'value' => $enum->value,
                    'group' => self::group($enum),
                    'group_slug' => Str::slug(self::group($enum)),
                    'description' => self::description($enum),
                ];
            }
        }

        return $permissions;
    }

    /**
     * Retrieves all the permission group names.
     */
    public static function groups(): array
    {
        return collect(self::cases())
            ->map(function ($enum) {
                return self::group($enum);
            })->filter(function ($name) {
                return ! is_null($name);
            })->unique()->values()->toArray();
    }

    /**
     * Retrieves all the permission group slug names.
     */
    public static function groupSlugs(): array
    {
        return Arr::map(self::groups(), function (string $groupName, int $key) {
            return Str::slug($groupName);
        });
    }

    /**
     * Retrieves all info of the ENUM by attributes, case and value.
     */
    public static function asAttributesArray(): array
    {
        /** @var array<string,string> $values */
        $values = collect(self::cases())
            ->map(function ($enum) {
                return [
                    'module' => self::module($enum),
                    'name' => $enum->name,
                    'value' => $enum->value,
                    'group' => self::group($enum),
                    'description' => self::description($enum),
                    'abilities' => self::abilities($enum),
                ];
            })->toArray();

        return $values;
    }

    /**
     * Retrieves permissions by group with all the informations available.
     */
    public static function byGroup(): array
    {
        return collect(self::asAttributesArray())->groupBy('group')->toArray();
    }

    /**
     * Retrieves permissions with all the informations available and groups them form PrimeVue Tree Component.
     */
    public static function byPrimeVueGroup(array $abilities = [], bool $isSuperAdmin = false): array
    {
        $groups = [];

        foreach (self::byGroup() as $groupName => $groupSets) {
            if ($groupName) {

                $results = array_values(array_filter(Arr::map($groupSets, function (array $groupSet, int $key) use($abilities, $isSuperAdmin) {
                    return self::prepareTreeItem($groupSet, $abilities, $isSuperAdmin);
                })));

                if(count($results) != 0)  {
                    array_push($groups, [
                        'label' => Str::headline($groupName),
                        'key' => Str::slug($groupName),
                        'description' => $groupName.' group permissions. Expand to see all modules.',
                        'children' => $results,
                    ]);
                }

            } else {
                foreach ($groupSets as $groupSet) {
                    $results = self::prepareTreeItem($groupSet, $abilities, $isSuperAdmin);

                    if(count($results) != 0){
                        array_push($groups, $results);
                    }

                }
            }
        }
        // exit;

        return $groups;
    }

    /**
     * Helper function for structuring PrimeVue tree groups.
     */
    private static function prepareTreeItem(array $groupSet, array $abilities = [], bool $isSuperAdmin = false): array
    {
        $accessPermissionStr = data_get($groupSet, 'value').'_'.data_get($groupSet, 'abilities.0');
        // dd($permission, $abilities);

        if(!$isSuperAdmin && !in_array($accessPermissionStr, $abilities)) return [];

        return [
            'label' => Str::headline($groupSet['module']),
            'key' => $groupSet['value'],
            'description' => $groupSet['description'],
            'children' => array_values(array_filter(Arr::map($groupSet['abilities'], function (string $permission, string $key) use ($groupSet, $abilities, $isSuperAdmin) {
                    if(!$isSuperAdmin && !in_array($groupSet['value'].'_'.$permission, $abilities)) return [];

                    return [
                        'key' => $groupSet['value'].'_'.$permission,
                        'label' => Str::headline($permission),
                        'module_key' => $groupSet['value'],
                    ];
                }))),
        ];
        // return [
        //     'label' => Str::headline($groupSet['module']),
        //     'key' => $groupSet['value'],
        //     'description' => $groupSet['description'],
        //     'children' => Arr::map($groupSet['abilities'], function (string $permission, string $key) use ($groupSet) {
        //         return [
        //             'key' => $groupSet['value'].'_'.$permission,
        //             'label' => Str::headline($permission),
        //             'module_key' => $groupSet['value'],
        //         ];
        //     }),
        // ];

    }

    /**
     * Gets the #Module attribute.
     */
    public static function module(BackedEnum $enum): string
    {
        $reflection = new ReflectionClassConstant(
            class: self::class,
            constant: $enum->name,
        );

        $attributes = $reflection->getAttributes(
            name: Module::class,
        );

        if (count($attributes) === 0) {
            return Str::headline(
                value: $enum->value
            );
        }

        return $attributes[0]->newInstance()->module;
    }

    /**
     * Gets the #Group attribute.
     */
    public static function group(BackedEnum $enum): ?string
    {
        $reflection = new ReflectionClassConstant(
            class: self::class,
            constant: $enum->name,
        );

        $attributes = $reflection->getAttributes(
            name: Group::class,
        );

        if (count($attributes) === 0) {
            return null;
        }

        return $attributes[0]->newInstance()->group;
    }

    /**
     * Gets the #Description attribute.
     */
    public static function description(BackedEnum $enum): string
    {
        $reflection = new ReflectionClassConstant(
            class: self::class,
            constant: $enum->name,
        );

        $attributes = $reflection->getAttributes(
            name: Description::class,
        );

        if (count($attributes) === 0) {
            return Str::headline(
                value: $enum->value
            );
        }

        return $attributes[0]->newInstance()->description;
    }

    /**
     * Gets the #Abilities attribute.
     */
    public static function abilities(BackedEnum $enum): array
    {
        $reflection = new ReflectionClassConstant(
            class: self::class,
            constant: $enum->name,
        );

        $attributes = $reflection->getAttributes(
            name: Abilities::class,
        );

        if (count($attributes) === 0) {
            return [Str::headline(
                value: strval($enum->value)
            )];
        }

        return $attributes[0]->newInstance()->abilities;
    }
}
