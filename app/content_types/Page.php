<?php

class Page extends Publishable {
	public $order;
	public $hidden = false;
	public $subpages = array(); //of type Page
}