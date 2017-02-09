<?php
/**
* 
*/
class Cammino_Affiliateclub_IndiqueController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$block = $this->getLayout()->createBlock('affiliateclub/indique');
		$this->loadLayout();
		$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
	}

	public function emailAction(){
		$helper = Mage::helper('affiliateclub');
		echo "ok";
		return;

		try{
			$mailer = Mage::getModel('core/email_template_mailer');
			$emailInfo = Mage::getModel('core/email_info');
			foreach ($_POST as $email) {
				if(strlen($email) > 0){
					$emailInfo->addTo($email, "");
				}
			}
			$mailer->addEmailInfo($emailInfo);
			$mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId));
			$mailer->setStoreId($storeId);
			$mailer->setTemplateId("indicated_coupon");
			$mailer->setTemplateParams(array(
					'indicatedCoupon' => $helper->getDefaultIndicatedCoupon(),
					'indicatedLink'   => $helper->getShareLink()
				)
			);

			if($mailer->send()){
				echo "Emails enviados";
				return true;
			}else{
				echo "nao pode enviar";
				return false;
			}
		}catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
	}

	public function loginAction(){
		$session = Mage::getSingleton('core/session');
		$session->setAffiliateclubLogin(true);
		$this->_redirect('customer/account/login');
	}
}