<?php

class School extends Eloquent {

	protected $table = 'schools';

    protected $visible = array('id', 'name', 'latitude', 'longitude', 'inspections', 'distance');

    public function inspections()
    {
        return $this->hasMany('SchoolInspection');
    }

}
