<?php
namespace SharedShopping;

class ValidationManager extends BaseObject
{
    /**
     * Validates a given input value to be a float with 2 decimal places.
     *
     * @return bool
     */
    public static function validateFloat(string $value) : bool {
        $regex = '/^(\d+(?:\.?\d{1,2})?)$/i';
        print_r(preg_match($regex, $value));
        return preg_match($regex, $value);
    }

    /**
     * Validates a given input value to be a pure integer, without decimal places
     *
     * @return bool
     */
    public static function validateInteger(string $value) : bool {
        $regex = '/^(\d*)$/i';
        print_r(preg_match($regex, $value));
        return preg_match($regex, $value);
    }
    
}