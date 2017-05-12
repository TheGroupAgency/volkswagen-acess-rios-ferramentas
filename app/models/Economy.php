<?php

class Economy extends Eloquent {
	public $timestamps = false;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'economy';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

	/**
	 * 
	 */
	protected $fillable = array('name');

}
