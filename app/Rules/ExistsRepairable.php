<?php

namespace App\Rules;

use App\Models\FreightWagon;
use App\Models\PassengerWagon;
use App\Models\TractiveUnit;
use Illuminate\Contracts\Validation\Rule;

class ExistsRepairable implements Rule
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
        return PassengerWagon::find($value) || FreightWagon::find($value) || TractiveUnit::find($value) ;
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
