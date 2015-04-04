<?php

class Property extends Eloquent {

	protected $table = 'properties';

    protected $fillable = array('street_address', 'city', 'postcode');

    protected $visible = array('id', 'street_address', 'city', 'postcode', 'latitude', 'longitude', 'ward');

    public function ward() {
        return $this->belongsTo('Ward');
    }

}
