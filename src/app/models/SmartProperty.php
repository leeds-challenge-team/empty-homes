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

        // Do we have lat and long for this?
        if ($property->latitude AND $property->longitude) {

            // Awesome, set the DB property so we know what we're doing.
            $this->db = $property;
            $this->source = 'database';

        } else {

            // We don't got it. Try geocode the address.

            try {
                $geocode = Geocoder::geocode($street_address . ', ' . $city . ', ' . $postcode);

                // If we've got to here, we've geocoded successfully.

                $property = new Property;

                $property->street_address = $street_address;
                $property->city = $city;
                $property->postcode = $postcode;
                $property->latitude = (float) ($geocode->getBounds()['north'] + $geocode->getBounds()['south'])/2;
                $property->longitude = (float) ($geocode->getBounds()['east'] + $geocode->getBounds()['west'])/2;

                $property->save();

                $this->db = $property;
                $this->source = 'geocoder';

            } catch (Exception $e) {

                // Something has gone AWOL doing geocoding.
                exit('bum');

            }

        }

        // Awesome, property sorted. Now try find the ward data from MapIt
        $mapit_data = json_decode(file_get_contents('http://mapit.mysociety.org/postcode/' . urlencode($property->postcode)));

        $ward_data = json_decode(file_get_contents('http://mapit.mysociety.org/area/' . $mapit_data->shortcuts->ward));

        $ward = Ward::firstOrNew(array('name' => $ward_data->name));

        if ($ward->mapit_id == null) {
            $ward->mapit_id = $mapit_data->shortcuts->ward;
            $ward->save();
        }

        $property->ward()->associate($ward);

        $property->save();

    }

}
