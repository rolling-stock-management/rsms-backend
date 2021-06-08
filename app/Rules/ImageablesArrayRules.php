<?php

namespace App\Rules;

use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\TractiveUnit;
use Illuminate\Contracts\Validation\Rule;

class ImageablesArrayRules implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //TODO: Refactoring possibilities.
        //Check if there is at least one id in the arrays (i.e. at leas one is not empty)
        $isPresent = !empty($value['passenger']) || !empty($value['freight']) || !empty($value['locomotive']);
        if(!$isPresent) return false;

        //Check if passenger wagons ids exist
        $passengerValidIds = true;
        foreach ($value['passenger'] as &$item) {
            $passengerValidIds = $passengerValidIds && PassengerWagon::find($item);
        }

        //Check if freight wagons ids exist
        $freightValidIds = true;
        foreach ($value['freight'] as &$item) {
            $freightValidIds = $freightValidIds && FreightWagon::find($item);
        }

        //Check if tractive units ids exist
        $locomotiveValidIds = true;
        foreach ($value['locomotive'] as &$item) {
            $locomotiveValidIds = $locomotiveValidIds && TractiveUnit::find($item);
        }

        return $passengerValidIds && $freightValidIds && $locomotiveValidIds;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must exist.';
    }
}
