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

	protected $clauseProperties = [
		'status',
		'flightNumber'
	];

    public function getFlights($parameters)
    {	
    	// http://api-airinfo.mr/api/v1/flights?include=departure,arrival
    	if(empty($parameters)){
        	return $this->filterFlights(Flight::all());
    	}

    	$withKeys = $this->getWithKeys($parameters);
    	
    	$whereClauses = $this->getWithClauses($parameters);

    	$flights = Flight::with($withKeys)->where($whereClauses)->get();

    	return $this->filterFlights($flights, $withKeys);
    }

    // public function getFlight($flightNumber){
    // 	return $this->filterFlights(Flight::where('flightNumber', $flightNumber)->get());
    // }


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

    protected function getWithKeys($parameters){

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

    	return $withKeys;
    }
    protected function getWithClauses($parameters){
    	// http://api-airinfo.mr/api/v1/flights?include=arrival,departure&status=delayed
    	$clause = [];

    	foreach ($this->clauseProperties as $prop) {

    		if(in_array($prop, array_keys($parameters))){
    			$clause[$prop] = $parameters[$prop];
    		}
    	}

    	return $clause;
    	// "status": "delayed"
    }
}