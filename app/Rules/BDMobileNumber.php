<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BDMobileNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /**
         * Explanation ::
         * - Optionally start with '+88'.
         * - Followed by '01'.
         * - Followed by a digit from 3 to 9.
         * - Followed by exactly 8 numeric digits.
         */
        $pattern = '/^(?:\+?88)?01[3-9]\d{8}$/';

        if (! preg_match($pattern, $value)) {
            $fail('Invalid mobile number provided.', null);
        }
    }
}
