<?php

class Cammino_Affiliateclub_Model_Mysql4_Affiliateclub extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the id refers to the key field in your database table.
        $this->_init('affiliateclub/affiliateclub', 'id');
    }
}