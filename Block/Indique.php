<?php
/**
* 
*/
class Cammino_Affiliateclub_Block_Indique extends Mage_Core_Block_Template
{
	
	protected function _construct() {
		$this->setTemplate('affiliateclub/indique.phtml');
		parent::_construct();
	}

    public function getShareLink(){
        $coupon = Mage::getStoreConfig('affiliateclub/affiliateclub_group/affiliateclub_indicated_coupon');
        $url = Mage::getStoreConfig('affiliateclub/affiliateclub_group/affiliateclub_share_url');
        return urlencode($url . $this->getCustomerEmail() . "&indicatedCoupon=" . $coupon);
    }

    public function getCustomerEmail(){
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
    		return "?indicatorEmail=" . $customer = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
    	}else{
    		return "";
    	}
	}

}