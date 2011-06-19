<?php

class Page extends Publishable {
	protected $order;
	protected $published;
	protected $subpages = array(); //of type Page
}