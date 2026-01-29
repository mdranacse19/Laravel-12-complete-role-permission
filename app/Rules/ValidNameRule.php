<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidNameRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim($value);

        if (!$value) {
            $fail('The :attribute field is required and cannot be empty or only spaces.');

            return;
        }

        // 1. Allow only letters, spaces, dot, hyphen, parentheses
        if (!preg_match("/^[\p{L}\p{N}\s.\-()'\p{Bengali}]+$/u", $value)) {
            $fail('The :attribute may only contain letters, numbers, spaces, dots, dashes, parentheses, apostrophes, and Bangla characters.');

            return;
        }

        // 2. At least one letter
        if (!preg_match('/\p{L}/u', $value)) {
            $fail('The :attribute must contain at least one letter.');

            return;
        }

        // 3. Must start with a letter or digit
        if (!preg_match('/^[\p{L}0-9]/u', $value)) {
            $fail('The :attribute must start with a letter or number.');

            return;
        }

        $endsWithValidChar = preg_match('/[\p{L}\p{N}\p{Bengali}]$/u', $value);
        $endsWithValidDotAfterChar = preg_match('/[\p{L}\p{N}\p{Bengali}]\.$/u', $value);
        $endsWithValidParen = preg_match('/\([\p{L}\p{N}\p{Bengali}][^)]*\)$/u', $value);
        $endsWithValidParenDot = preg_match('/\([\p{L}\p{N}\p{Bengali}][^)]*\)\.$/u', $value);
        $endsWithMultipleDots = preg_match('/\.{2,}$/', $value);

        if (!($endsWithValidChar || $endsWithValidDotAfterChar || $endsWithValidParen || $endsWithValidParenDot) || $endsWithMultipleDots) {
            $fail('The :attribute must end with a letter, number, a dot (after letter or parenthesis), or valid parenthesis.');

            return;
        }

        // Reject multiple apostrophes in a row
        if (str_contains($value, "''")) {
            $fail('The :attribute must not contain consecutive apostrophes.');

            return;
        }

        // Reject if starts or ends with apostrophe
        if (str_starts_with($value, "'") || str_ends_with($value, "'")) {
            $fail('The :attribute must not start or end with an apostrophe.');
        }
    }
}
