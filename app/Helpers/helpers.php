<?php

if (!function_exists('userAbilities')) {
    function userAbilities($user = null)
    {
        $user = $user ?? auth()->user();

        $abilities = [];

        if ($user?->roles->isNotEmpty()) {
            $abilities = $user?->roles->first()
                ->permissions()
                ->pluck('name')
                ->unique()
                ->values()
                ->toArray() ?: [];
        }

        return $abilities;
    }
}
