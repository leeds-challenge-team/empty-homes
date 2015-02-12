<?php

class LongTermVoid extends Eloquent {

	protected $table = 'long_term_voids';

    public function getDates()
    {
        return array(
            'date',
        );
    }

    public function ward()
    {
        return $this->belongsTo('Ward');
    }

}
