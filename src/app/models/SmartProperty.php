<?php

class SmartProperty {

    public $db;
    public $source;

    public function __construct ($street_address, $city, $postcode) {

        // First, try get this from the database?
        $property = Property::firstOrNew(array(
            'street_address' => $street_address,
            'city' => $city,
            'postcode' => $postcode
        ));

        $this->db = $property;

        // Do we have lat and long for this?
        if ($property->latitude AND $property->longitude) {

            // Awesome, set the DB property so we know what we're doing.
            $this->source = 'database';

        } else {

            // We don't got it. Try geocode the address.

            try {
                $geocode = Geocoder::geocode($street_address . ', ' . $city . ', ' . $postcode);

                // If we've got to here, we've geocoded successfully.

                $property->latitude = (float) ($geocode->getBounds()['north'] + $geocode->getBounds()['south'])/2;
                $property->longitude = (float) ($geocode->getBounds()['east'] + $geocode->getBounds()['west'])/2;

                $property->save();

                $this->source = 'geocoder';

                // Because we geocoded, cool off for half a second
                sleep(0.5);

            } catch (Exception $e) {

                // Something has gone AWOL doing geocoding.
                // throw new Exception('Couldn\'t geocode ' . $property->street_address . ': ' . $e->getMessage());

                $this->source = 'plain';

            }

        }

        // Awesome, property sorted. Now, ward.

        if (!$property->ward) {

            $mapit_data = json_decode(file_get_contents('http://mapit.mysociety.org/postcode/' . urlencode($property->postcode)));

            $ward_data = json_decode(file_get_contents('http://mapit.mysociety.org/area/' . $mapit_data->shortcuts->ward));

            $ward = Ward::firstOrNew(array('name' => $ward_data->name));

            if ($ward->mapit_id == null) {
                $ward->mapit_id = $mapit_data->shortcuts->ward;
                $ward->save();
            }

            $property->ward()->associate($ward);

            // Done a mapIt hit, sleep for a second.
            sleep(1);

        }

        $property->save();

    }

}
