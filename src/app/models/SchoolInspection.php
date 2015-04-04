<?php

class SchoolInspection extends Eloquent {

	protected $table = 'ofsted_inspections';

    protected $visible = array('id', 'start_date', 'end_date', 'overall_rating');

    public function getDates()
    {
        return array(
            'start_date',
            'end_date'
        );
    }

    public function school()
    {
        return $this->belongsTo('School');
    }

}
