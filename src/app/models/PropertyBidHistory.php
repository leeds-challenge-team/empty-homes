<?php

class PropertyBidHistory extends Eloquent {

    protected $fillable = array('date', 'interest_count');

    protected $visible = array('date', 'interest_count');

	protected $table = 'property_bid_history';

    public function getDates()
    {
        return array(
            'date'
        );
    }

    public function property() {
        return $this->belongsTo('Property');
    }

}
