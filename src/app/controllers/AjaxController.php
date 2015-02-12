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

}
