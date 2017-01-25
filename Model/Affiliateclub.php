<?php

class Cammino_Affiliateclub_Model_Affiliateclub extends Mage_Core_Model_Abstract
{
	
	public function __construct()
	{
		parent::_construct();
        $this->_init('affiliateclub/affiliateclub');
	}
}