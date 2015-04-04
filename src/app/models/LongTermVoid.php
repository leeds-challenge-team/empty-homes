<?php

class LongTermVoid extends Eloquent {

	protected $table = 'long_term_voids';

    protected $visible = array('date', 'count');

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
