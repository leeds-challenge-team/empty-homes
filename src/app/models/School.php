<?php

class School extends Eloquent {

	protected $table = 'schools';

    public function inspections()
    {
        return $this->hasMany('SchoolInspection');
    }

}
