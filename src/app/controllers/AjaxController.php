<?php

class AjaxController extends BaseController {

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

    public function showPois()
    {

        if (!Input::get('lat') OR !Input::get('lat')) {

            // No ward name specified, 400 out.
            $data = array(
                'success' => false,
                'message' => 'You must provide a latitude and longitude!'
            );
            $code = 400;

        }elseif (!Input::get('types')) {

            // No ward name specified, 400 out.
            $data = array(
                'success' => false,
                'message' => 'You must provide a list of valid types!'
            );
            $code = 400;

        } else {

            $lat = (float) Input::get('lat');
            $lon = (float) Input::get('lon');

            $distance = (float) Input::get('distance') ?: '2';

            // Exploding types!
            $types = explode(',', Input::get('types'));

            $data['success'] = true;
            $data['types'] = $types;

            $data['pois'] = array();

            if (in_array('school', $types)) {

                $schools = School::
                    select(DB::raw('*, (
                        3959 * acos (
                            cos ( radians(' . (float) $lat . ') )
                            * cos( radians( latitude ) )
                            * cos( radians( longitude ) - radians(' . (float) $lon . ') )
                            + sin ( radians(' . (float) $lat . ') )
                            * sin( radians( latitude ) )
                        )
                    ) as distance'))
                    ->having('distance', '<', (int) $distance < 5 ? (int) $distance : 5)
                    ->orderBy('distance')
                    ->get();

                foreach ($schools as $school) {
                $data['pois'][] = array(
                    'type' => 'school',
                    'name' => $school->name,
                    'lat' => (float) $school->latitude,
                    'lon' => (float) $school->longitude,
                    'distance' => round($school->distance, 2)
                );
            }

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
