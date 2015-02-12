<?php

class Ward extends Eloquent {

	protected $table = 'wards';

    public function longTermVoids()
    {
        return $this->hasMany('LongTermVoid');
    }

}
