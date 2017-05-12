<?php

class Oil extends Eloquent {
	public $timestamps = false;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'oil';

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
