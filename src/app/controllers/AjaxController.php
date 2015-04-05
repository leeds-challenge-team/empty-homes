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

        // Go retrieve the property!
        $property = new SmartProperty(Input::get('address'), 'Leeds', Input::get('postcode'));

        // Dump the property object to output
        $data['property'] = $property->db;

        $property->db->ward->load('longTermVoids');

        // Make sure we have at least empty POI arrays
        $data['pois'] = array(
            'schools' => array(),
            'bid_properties' => array()
        );

        // Get the Schools from the database
        $schools = School::
            select(DB::raw('*, (
                3959 * acos (
                    cos ( radians(' . $property->db->latitude . ') )
                    * cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians(' . $property->db->longitude . ') )
                    + sin ( radians(' . $property->db->latitude . ') )
                    * sin( radians( latitude ) )
                )
            ) as distance'))
            ->having('distance', '<', 2)
            ->orderBy('distance')
            ->get();

        // Build up the schools POI array
        foreach ($schools as $school) {
            $data['pois']['schools'][] = $school->load('inspections');
        }

        // Get the properties from the database
        $bid_properties = Property::
            select(DB::raw('*, (
                3959 * acos (
                    cos ( radians(' . $property->db->latitude . ') )
                    * cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians(' . $property->db->longitude . ') )
                    + sin ( radians(' . $property->db->latitude . ') )
                    * sin( radians( latitude ) )
                )
            ) as distance'))
            ->having('distance', '<', 2)
            ->orderBy('distance')
            ->has('bidHistory')
            ->with('bidHistory')
            ->get();

        // Build up the schools POI array
        foreach ($bid_properties as $bid_property) {
            $data['pois']['bid_properties'][] = $bid_property;
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
