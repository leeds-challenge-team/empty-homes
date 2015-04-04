<?php

class Ward extends Eloquent {

	protected $table = 'wards';

    protected $fillable = array('name', 'mapit_id');

    protected $visible = array('id', 'name', 'mapit_id', 'longTermVoids');

    public function longTermVoids()
    {
        return $this->hasMany('LongTermVoid');
    }

    public function properties()
    {
        return $this->hasMany('Property');
    }

}
