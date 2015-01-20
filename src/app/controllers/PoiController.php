<?php

class PoiController extends BaseController {

	public function showPois($lat, $lon, $distance)
	{

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
        	echo '<p>' . $school->name . ': ' . $school->distance . '</p>';
        }
	}

}
