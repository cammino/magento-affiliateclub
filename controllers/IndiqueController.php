<?php
/**
* 
*/
class Cammino_Affiliateclub_IndiqueController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
		$block = $this->getLayout()->createBlock('affiliateclub/indique');
		$this->loadLayout();
		$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
	}

	public function emailAction()
	{
		$helper = Mage::helper('affiliateclub');

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
					'indicatorName' 	=> $helper->getCustomerLoggedName(),
					'indicatedLink'   	=> $helper->getShareLink()
				)
			);

			if($mailer->send()){
				echo "ok";
			}else{
				echo "não pode enviar";
			}
		}catch (Exception $e) {
            echo $e->getMessage();
        }
        return;
	}

	public function loginAction()
	{
		$session = Mage::getSingleton('core/session');
		$session->setAffiliateclubLogin(true);
		$this->_redirect('customer/account/login');
	}
}