<?php

namespace App\Observers;

use App\Models\PassengerWagon;
use App\Models\PassengerWagonType;

class PassengerWagonObserver
{
    /**
     * Handle the PassengerWagon "creating" event.
     * Assign the propper wagon type id to the wagon that is being created.
     *
     * @param  \App\Models\PassengerWagon  $passengerWagon
     * @return void
     */
    public function creating(PassengerWagon $passengerWagon)
    {
        $wagonType = substr($passengerWagon->number, 4, 2).'-'.substr($passengerWagon->number, 6, 2);
        $wagonTypeInstance = PassengerWagonType::where('name', $wagonType)->firstOrFail();
        $passengerWagon->type_id = $wagonTypeInstance->id;

        $this->CalculateRepairValidUntilDate($passengerWagon, $wagonTypeInstance);
    }

    /**
     * Handle the PassengerWagon "updating" event.
     *  Assign the propper wagon type id to the wagon that is being update.
     *
     * @param  \App\Models\PassengerWagon  $passengerWagon
     * @return void
     */
    public function updating(PassengerWagon $passengerWagon)
    {
        $wagonType = substr($passengerWagon->number, 4, 2).'-'.substr($passengerWagon->number, 6, 2);
        $wagonTypeInstance = PassengerWagonType::where('name', $wagonType)->firstOrFail();
        $passengerWagon->type_id = $wagonTypeInstance->id;

        $this->CalculateRepairValidUntilDate($passengerWagon, $wagonTypeInstance);
    }

    private function CalculateRepairValidUntilDate(PassengerWagon $passengerWagon, PassengerWagonType $wagonType)
    {
        if($passengerWagon->repair_date != null)
        {
            $timeToAdd = $wagonType->repair_valid_for;
            $revisionDate = $passengerWagon->repair_date;
            $expirationDate = $revisionDate->addYears($timeToAdd);
            $passengerWagon->repair_valid_until = $expirationDate;
        }
        else
        {
            $passengerWagon->repair_valid_until = '';
        }
    }
}
