<?php
namespace App\Services\v1;

use App\Flight;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FlightService
{	
	protected $supportedIncludes = [
		'arrivalAirport' =>'arrival',
		'departureAirport' => 'departure'
	];

    public function getFlights($parameters)
    {	
    	// http://api-airinfo.mr/api/v1/flights?include=departure,arrival
    	if(empty($parameters)){
        	return $this->filterFlights(Flight::all());
    	}

    	$withKeys = [];

    	if(isset($parameters['include'])){
    		$includeParms = explode(',', $parameters['include']);
    		//departure, arrival
    		$includes = array_intersect($this->supportedIncludes, $includeParms);
    		// intersect mainly je value gula mile tader k alada kore,
    		// {"arrivalAirport": "arrival", "departureAirport": "departure"}
    		$withKeys = array_keys($includes);
    		// array keys sudu matro key gula k alada kore... {arrivalAirport, departureAirport}
    	}

    	return $this->filterFlights(Flight::with($withKeys)->get(), $withKeys);
    }

    public function getFlight($flightNumber){
    	return $this->filterFlights(Flight::where('flightNumber', $flightNumber)->get());
    }


    protected function filterFlights($flights, $keys=[]){
    	$data = [];

    	foreach ($flights as $flight) {
    		$entry = [
    			'flightNumber' => $flight->flightNumber,
    			'status' => $flight->status,
    			'href' => route('flights.show', ['id' => $flight->flightNumber])
    		];

    		if(in_array('arrivalAirport', $keys)){
    			$entry['arrival'] = [
    				'datetime' => $flight->arrivalDateTime,
    				'iataCode' => $flight->arrivalAirport->iataCode,
    				'city' => $flight->arrivalAirport->city,
    				'state' => $flight->arrivalAirport->state,
    			];
    		}

    		if(in_array('departureAirport', $keys)){
    			$entry['departure'] = [
    				'datetime' => $flight->departureDateTime,
    				'iataCode' => $flight->departureAirport->iataCode,
    				'city' => $flight->departureAirport->city,
    				'state' => $flight->departureAirport->state,
    			];
    		}
    		
    		$data[] = $entry;
    	}

    	return $data;
    }
}
