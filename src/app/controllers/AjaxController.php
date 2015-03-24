<?php

class AjaxController extends BaseController {

    public function propertyInfo()
    {

        if (!Input::get('address')) {
            throw new \Exception('You must provide an address!');
        }

        if (!Input::get('postcode')) {
            throw new \Exception('You must provide a postcode!');
        }

        // Try geocode the address
        $geocode = Geocoder::geocode(Input::get('address') . ', Leeds, ' . Input::get('postcode'));

        // If we've got to here, we've geocoded successfully.

        // Build the property array, this comes in useful later.
        $property = array(
            'street_number' => $geocode->getStreetNumber(),
            'street_name'   => $geocode->getStreetName(),
            'city'          => $geocode->getCity(),
            'postcode'      => $geocode->getZipcode(),
            'latitude'      => (float) ($geocode->getBounds()['north'] + $geocode->getBounds()['south'])/2,
            'longitude'      => (float) ($geocode->getBounds()['east'] + $geocode->getBounds()['west'])/2
        );

        // Dump the property array to output
        $data['property'] = $property;

        // Try find the ward data from MapIt
        $mapit_data = json_decode(file_get_contents('http://mapit.mysociety.org/postcode/' . urlencode($property['postcode'])));

        $ward_data = json_decode(file_get_contents('http://mapit.mysociety.org/area/' . $mapit_data->shortcuts->ward));

        // Look up the ward in our internal listing
        $ward = Ward::where('name', '=', $ward_data->name)->with('longTermVoids')->first();

        $data['ward']['name'] = $ward->name;

        $data['ward']['void_count_history'] = array();

        foreach ($ward->longTermVoids as $ltv){
            $data['ward']['void_count_history'][] = array(
                'date' => $ltv->date->toIso8601String(),
                'count' => (int) $ltv->count
            );
        }

        // Make sure we have at least empty POI arrays
        $data['pois'] = array(
            'schools' => array()
        );

        // Get the Schools from the database
        $schools = School::
            select(DB::raw('*, (
                3959 * acos (
                    cos ( radians(' . $property['latitude'] . ') )
                    * cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians(' . $property['longitude'] . ') )
                    + sin ( radians(' . $property['latitude'] . ') )
                    * sin( radians( latitude ) )
                )
            ) as distance'))
            ->having('distance', '<', 2)
            ->orderBy('distance')
            ->get();

        // Build up the schools POI array
        foreach ($schools as $school) {
            $data['pois']['schools'][] = array(
                'name' => $school->name,
                'lat' => (float) $school->latitude,
                'lon' => (float) $school->longitude,
                'distance' => round($school->distance, 2),
                'inspections' => $school->inspections()->slim()->orderBy('start_date', 'desc')->get()
            );
        }

        // Assemble the response
        $response = Response::json($data);

        // If we have a callback specified, let the renderer know
        if (Input::get('callback')) {
            $response->setCallback(Input::get('callback'));
        }

        // Send it!
        return $response;

    }

	public function longTermVoidsByWard()
	{

        if (!Input::get('name')) {

            // No ward name specified, 400 out.
            $data = array(
                'success' => false,
                'message' => 'You must provide a ward name!'
            );
            $code = 400;

        } else {

            // Try find the requested ward
            $ward = Ward::where('name', '=', Input::get('name'))->with('longTermVoids')->first();

            if ($ward) {

                // We've got it, data up!
                $data['success'] = true;
                $data['ward'] = $ward;

            } else {

                // Couldn't find that ward, 404 out

                $data = array(
                'success' => false,
                'message' => 'Unable to find ward!'
                );
                $code = 404;

            }
        }

        // Make sure we send a 200
        if (empty($code)){
            $code = 200;
        }

        // Assemble the response!
		$response = Response::json($data, $code);

        if (Input::get('callback')) {
            $response->setCallback(Input::get('callback'));
        }

        return $response;

	}

}
