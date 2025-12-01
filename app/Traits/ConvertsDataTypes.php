<?php

namespace App\Traits;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

trait ConvertsDataTypes
{
    /**
     * Return integer value.
     */
    public function convertInteger(string $field): ?int
    {
        $integer = $this->validated($field);

        return is_null($integer) ? null : (int) $this->validated($field);
    }

    /**
     * Return cleared string value from array.
     */
    public function convertArrayString(string $field, bool $purify = true): ?array
    {
        $data = [];

        foreach ($this->get($field) as $input => $value) {
            $string = $this->clearString((string) ($value ?? ''), false);
            $data[$input] = $purify ? Purify::clean($string) : $string;
        }

        return $data;
    }

    /**
     * Return cleared string value.
     */
    public function convertString(string $field, bool $purify = true): ?string
    {
        $string = $this->clearString((string) ($this->get($field) ?? ''), false);

        return $purify ? Purify::clean($string) : $string;
    }

    /**
     * Return cleared string value of array input.
     *
     * @structure   inputname[key]
     */
    public function convertInputArrayString(string $field, bool $purify = true): array
    {
        $inputData = $this->validated($field);

        if (! $inputData || ! is_array($inputData)) {
            return [];
        }

        $result = [];

        foreach ($this->get($field) as $key => $item) {
            $string = $purify ? Purify::clean($this->clearString($item, false)) : $this->clearString($item, false);
            $result[$key] = $string;
        }

        return $result;
    }

    /**
     * Return cleared string value of array input where keys were indexed.
     *
     * @structure   inputname[index][key]
     */
    public function convertIndexedInputArrayString(string $field, bool $purify = true): array
    {
        $inputData = $this->validated($field);

        if (! $inputData || ! is_array($inputData)) {
            return [];
        }

        $result = [];

        foreach ($this->get($field) as $i => $items) {
            foreach ($items as $key => $input) {
                $string = $purify ? Purify::clean($this->clearString($input, false)) : $this->clearString($input, false);
                $result[$i][$key] = $string;
            }
        }

        return $result;
    }

    /**
     * Return cleared string converted to slug.
     */
    public function convertSlug(string $field, string $alternateField = ''): ?string
    {
        return Str::slug($this->get($field) ? $this->convertString($field) : $this->convertString($alternateField));
    }

    /**
     * Sanitizes string value.
     */
    public function clearString(?string $string, bool $keepNewlines = true): ?string
    {
        if ($string === null) {
            return null;
        }

        if ($string === '') {
            return null;
        }

        if (str_replace(' ', '', $string) === '<p><br></p>') {
            return null;
        }

        $search = [
            "\0", // NUL
            "\f", // form feed
            "\v", // vertical tab
            "\u{0001}", // start of heading
            "\u{0002}", // start of text
            "\u{0003}", // end of text
            "\u{0004}", // end of transmission
            "\u{0005}", // enquiry
            "\u{0006}", // ACK
            "\u{0007}", // BEL
            "\u{0008}", // backspace
            "\u{000E}", // shift out
            "\u{000F}", // shift in
            "\u{0010}", // data link escape
            "\u{0011}", // DC1
            "\u{0012}", // DC2
            "\u{0013}", // DC3
            "\u{0014}", // DC4
            "\u{0015}", // NAK
            "\u{0016}", // SYN
            "\u{0017}", // ETB
            "\u{0018}", // CAN
            "\u{0019}", // EM
            "\u{001A}", // SUB
            "\u{001B}", // escape
            "\u{001C}", // file separator
            "\u{001D}", // group separator
            "\u{001E}", // record separator
            "\u{001F}", // unit separator
            "\u{007F}", // DEL
            "\u{00A0}", // non-breaking space
            "\u{1680}", // ogham space mark
            "\u{180E}", // mongolian vowel separator
            "\u{2000}", // en quad
            "\u{2001}", // em quad
            "\u{2002}", // en space
            "\u{2003}", // em space
            "\u{2004}", // three-per-em space
            "\u{2005}", // four-per-em space
            "\u{2006}", // six-per-em space
            "\u{2007}", // figure space
            "\u{2008}", // punctuation space
            "\u{2009}", // thin space
            "\u{200A}", // hair space
            "\u{200B}", // zero width space
            "\u{202F}", // narrow no-break space
            "\u{3000}", // ideographic space
            "\u{FEFF}", // zero width no -break space
        ];

        $replace = "\x20"; // plain old normal space
        $string = str_replace($search, $replace, $string);
        $secondSearch = $keepNewlines ? ["\r"] : ["\r", "\n", "\t", "\036", "\025"];
        $string = str_replace($secondSearch, '', $string);

        return trim($string);
    }

    /**
     * Return string value with newlines.
     */
    public function stringWithNewlines(string $field): string
    {
        return $this->clearString((string) ($this->get($field) ?? ''));
    }

    /**
     * Converts comma seperated value to array.
     */
    public function arrayFromValue($array): ?array
    {
        if (is_array($array)) {
            return $array;
        }

        if ($array === null) {
            return null;
        }

        if (is_string($array)) {
            return explode(',', $array);
        }

        return null;
    }

    /**
     * Converts array comma seperated value.
     */
    public function arrayFromString($array): ?string
    {
        if (is_string($array)) {
            return $array;
        }

        if ($array === null) {
            return null;
        }

        if (is_array($array)) {
            return implode(',', $array);
        }

        return null;
    }

    /**
     * Converts a field value to boolean.
     */
    public function convertBoolean(string $field): bool
    {
        $value = $this->validated($field);

        if ($value === null) {
            return false;
        }

        if ($value === '') {
            return false;
        }

        if ($value === 'true') {
            return true;
        }

        if ($value === 'yes') {
            return true;
        }

        if ($value == '1') {
            return true;
        }

        return false;
    }

    /**
     * Converts a value to Carbon date instance.
     */
    public function dateFromValue(?string $date): ?Carbon
    {
        if ($date === null) {
            return null;
        }

        if ($date === '') {
            return null;
        }

        $carbon = null;

        try {
            $carbon = Carbon::createFromFormat('d/m/Y', $date);
        } catch (InvalidFormatException $e) {
            // @ignoreException
        }

        if ($carbon === null) {
            Log::debug(sprintf('Invalid date: %s', $date));

            return null;
        }

        Log::debug(sprintf('Date object: %s (%s)', $carbon->toW3cString(), $carbon->getTimezone()));

        return $carbon;
    }

    /**
     * Return floating value.
     */
    public function convertFloat(string $field): ?float
    {
        $float = $this->validated($field);

        if ($float === null) {
            return null;
        }

        return (float) $float;
    }

    /**
     * Returns all data in the request, or omits the field if not set,
     * according to the config from the request. This is the way.
     */
    public function getAllData(array $fields): array
    {
        $return = [];

        foreach ($fields as $field => $info) {
            if ($this->has($info[0])) {
                $method = $info[1];
                $return[$field] = $this->$method($info[0]);
            }
        }

        return $return;
    }

    /**
     * Returns carbon date or NULL.
     */
    public function getCarbonDate(string $field): ?Carbon
    {
        $result = null;

        try {
            $result = $this->validated($field) ? Carbon::createFromFormat('d/m/Y', $this->validated($field)) : null;
        } catch (InvalidFormatException $e) {
            // @ignoreException
        }

        if ($result === null) {
            Log::debug(sprintf('Exception when parsing date "%s".', $this->validated($field)));
        }

        return $result;
    }

    /**
     * Return a carbon date or NULL from input type date.
     */
    public function getCarbonDateInput(string $field, ?string $format = 'Y-m-d'): ?Carbon
    {
        $result = null;

        // Check if the day string is available in the defined format
        $hasDay = boolval(strpos($format, 'd'));
        // explicitly add the day string
        $format = $hasDay ?: $format.'-d';
        // add the first day of the month as day
        $fieldVal = $hasDay ? $this->validated($field) : $this->validated($field).'-01';

        try {
            $result = $this->validated($field) ? Carbon::createFromFormat($format, $fieldVal) : null;
        } catch (InvalidFormatException $e) {
            // @ignoreException
        }

        if ($result === null) {
            Log::debug(sprintf('Exception when parsing date "%s".', $this->validated($field)));
        }

        return $result;
    }

    /**
     * Parsed date for carbon.
     */
    public function parseCarbon($field): ?Carbon
    {
        return $this->validated($field) ? Carbon::parse($this->validated($field))->timezone(config('app.timezone')) : null;
    }

    /**
     * Parse to integer
     */
    public function integerFromValue(?string $string): ?int
    {
        if ($string === null) {
            return null;
        }

        if ($string === '') {
            return null;
        }

        return (int) $string;
    }

    /**
     * Return integer value, or NULL when it's not set.
     */
    public function nullableInteger(string $field): ?int
    {
        if (! $this->has($field)) {
            return null;
        }

        $value = (string) $this->validated($field);

        if ($value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * Returns clean YouTube video URL.
     */
    public function cleanYouTubeURL(string $field): ?string
    {
        $videoID = $this->youTubeVideoID($field);

        return $videoID ? 'https://www.youtube.com/watch?v='.$videoID : null;
    }

    /**
     * Returns YouTube video query string.
     */
    public function youTubeVideoID(string $field): ?string
    {
        $URL = $this->convertString($field);

        if (! $URL) {
            return null;
        }

        $parsed_url = parse_url($URL);

        if (! array_key_exists('query', $parsed_url)) {
            return null;
        }

        parse_str($parsed_url['query'], $query);

        return array_key_exists('v', $query) ? $query['v'] : null;
    }

    /**
     * Returns YouTube video IDs from array.
     */
    public function youTubeVideoIDs(string $field): ?array
    {
        $IDs = [];

        foreach ($this->get($field) as $string) {
            $URL = $this->clearString($string);

            if (! $URL) {
                continue;
            }

            $parsed_url = parse_url($URL);

            if (! array_key_exists('query', $parsed_url)) {
                return null;
            }

            parse_str($parsed_url['query'], $query);

            if (array_key_exists('v', $query)) {
                array_push($IDs, $query['v']);
            }
        }

        return count($IDs) > 0 ? $IDs : null;
    }

    /**
     * Return json encode value.
     */
    public function convertJson($field): ?string
    {
        $array = $this->validated($field);

        if ($array === null) {
            return null;
        }

        return json_encode($array);
    }
}
