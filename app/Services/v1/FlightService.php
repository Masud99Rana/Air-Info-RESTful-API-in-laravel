<?php
namespace App\Services\v1;

use App\Flight;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FlightService
{
    public function getFlights()
    {
        return $this->filterFlights(Flight::all());
    }

    public function getFlight($flightNumber){
    	return $this->filterFlights(Flight::where('flightNumber', $flightNumber)->get());
    }


    protected function filterFlights($flights){
    	$data = [];

    	foreach ($flights as $flight) {
    		$entry = [
    			'flightNumber' => $flight->flightNumber,
    			'status' => $flight->status,
    			'href' => route('flights.show', ['id' => $flight->flightNumber])
    		];
    		
    		$data[] = $entry;
    	}

    	return $data;
    }
}
