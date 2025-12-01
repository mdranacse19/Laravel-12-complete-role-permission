<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $model->generateSlugOnCreate();
        });

        static::updating(function ($model) {
            $model->generateSlugOnUpdate();
        });
    }

    protected function generateSlugOnCreate(): void
    {
        if (empty($this->{$this->getSlugColumn()})) {
            $this->{$this->getSlugColumn()} = $this->generateUniqueSlug($this->{$this->getSlugSourceColumn()});
        }
    }

    protected function generateSlugOnUpdate(): void
    {
        if ($this->isDirty($this->getSlugSourceColumn())) {
            $this->{$this->getSlugColumn()} = $this->generateUniqueSlug($this->{$this->getSlugSourceColumn()});
        }
    }

    protected function generateUniqueSlug(string $value): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $i = 1;

        while (self::query()->where($this->getSlugColumn(), $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Override this method in your model to set the source column for slug
     */
    protected function getSlugSourceColumn(): string
    {
        return property_exists($this, 'slugFrom') ? $this->slugFrom : 'title';
    }

    /**
     * Override this method in your model to set the slug column
     */
    protected function getSlugColumn(): string
    {
        return property_exists($this, 'slugColumn') ? $this->slugColumn : 'slug';
    }
}
