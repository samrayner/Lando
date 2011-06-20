<?php

class Page extends Publishable {
	public $order;
	public $published;
	public $subpages = array(); //of type Page
}