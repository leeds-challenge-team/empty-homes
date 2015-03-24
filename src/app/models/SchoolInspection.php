<?php

class SchoolInspection extends Eloquent {

	protected $table = 'ofsted_inspections';

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

    public function scopeSlim($query)
    {
        return $query->select('id', 'start_date', 'end_date', 'overall_rating');
    }

}
