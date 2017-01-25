<?php

class Cammino_Affiliateclub_Model_Mysql4_Affiliateclub_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliateclub/affiliateclub');
    }
}