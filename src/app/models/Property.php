<?php

class Property extends Eloquent {

	protected $table = 'properties';

    protected $fillable = array('street_address', 'city', 'postcode');

    protected $visible = array('id', 'street_address', 'city', 'postcode', 'latitude', 'longitude', 'ward', 'bidHistory', 'distance');

    public function ward() {
        return $this->belongsTo('Ward');
    }

    public function bidHistory() {
        return $this->hasMany('PropertyBidHistory');
    }

}
