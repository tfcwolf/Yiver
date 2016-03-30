<?php
  namespace cms\Facades;
  use cms\Facades\Facades;
  class View extends Facades{

  	public static function getAccessName()
  	{
  		return '\cms\View\Views';
  	}
  }