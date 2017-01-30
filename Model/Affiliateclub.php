<?php

class Cammino_Affiliateclub_Model_Affiliateclub extends Mage_Core_Model_Abstract
{
	
	public function __construct()
	{
		parent::_construct();
        $this->helper = Mage::helper('affiliateclub');
        $this->_init('affiliateclub/affiliateclub');
	}

	/**
    * Aplica o cupom de desconto manualmente
    *
    * @return boolean
    */
    public function applyCoupon($couponCode)
    {
        $couponCode = (string) $couponCode;

        if (!$couponCode or !strlen($couponCode)) {
            return false;
        }

        try{
            $session = Mage::getSingleton('checkout/session');
            $cart = Mage::getSingleton('checkout/cart')->getQuote();
            $cart->getShippingAddress()->setCollectShippingRates(true);
            $cart->setCouponCode(strlen($couponCode) ? $couponCode : '')->collectTotals()->save();

            $this->helper->log("Aplicou o cupom: " . $couponCode);
            return true;

        }catch (Mage_Core_Exception $e) {
            $this->helper->log("Erro ao aplicar o cupom: " . $couponCode . ", Detalhes erro: " . $e);
        } catch (Exception $e) {
            $this->helper->log("Erro ao aplicar o cupom: " . $couponCode . ", Detalhes erro: " . $e);
        }
    }


    /**
    * Salva informações do pedido feito por indicação no banco
    *
    * @return null
    */
    public function saveAffiliateOrder($order, $indicatorEmail)
    {
        $dateNow = new DateTime();
        
        $indicated = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $indicator = Mage::getModel("customer/customer")->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($indicatorEmail);

        $data = array(
            'indicator_id'          => $indicator->getId(),
            'indicator_email'       => $indicatorEmail,
            'indicated_id'          => $indicated->getId(),
            'indicated_email'       => $indicated->getEmail(),
            'indicated_coupon'      => $order->coupon_code,
            'order_id'              => $order->getId(),
            'order_increment_id'    => $order->getIncrementId(),
            'created_at'            => $dateNow->getTimestamp(),
            'updated_at'            => $dateNow->getTimestamp()
        );
        
        $model = Mage::getModel('affiliateclub/affiliateclub')->addData($data);
        
        try {
            $model->save();
        } catch (Exception $e) {
            $this->helper->log($e->getMessage());
        }
    }

    /**
    * Gera um cupom de desconto único
    *
    * @return string
    */
    public function generateCoupon()
    {
        $coupon = false;

        try{
            $generator = Mage::getModel('salesrule/coupon_massgenerator');
            $generator->setFormat( Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC );
            $generator->setDash(0);
            $generator->setLength(12);
            $generator->setPrefix("");
            $generator->setSuffix("");

            $rule = Mage::getModel('salesrule/rule')->load(3);
            $rule->setCouponCodeGenerator($generator);
            $rule->setCouponType( Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO );

            $coupon = $rule->acquireCoupon();
            $coupon->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)->save();

            $this->helper->log("Gerou o cupom: " . $coupon->getCode());
            return $coupon->getCode();

        } catch (Exception $e) {
            $this->helper->log($e->getMessage());
        }
    }

    /**
    * Insere o cupom gerado para o indicador
    *
    * @return boolean
    */
    public function saveIndicatorCoupon($order, $indicatorCoupon)
    {
        try{
            $dateNow = new DateTime();
            $collection = mage::getModel('affiliateclub/affiliateclub')
            ->getCollection()
            ->addFieldToFilter('order_id', $order->getId());

            $collection->getFirstItem()
                       ->setIndicatorCoupon($indicatorCoupon)
                       ->setUpdatedAt($dateNow->getTimestamp())
                       ->save();
            
            $this->helper->log("Atualizou o cupom do indicador no banco");
        } catch (Exception $e) {
            $this->helper->log($e->getMessage());
        }        
    }

    public function sendEmailIndicatorCoupon($indicatorName, $indicatorEmail, $indicatorCoupon)
    {
    	try{
            $indicatorLink = Mage::getBaseUrl() . '?indicatorCoupon=' . $indicatorCoupon;

			$mailer = Mage::getModel('core/email_template_mailer');
			$emailInfo = Mage::getModel('core/email_info');
			$emailInfo->addTo($indicatorEmail, $indicatorName);

			$mailer->addEmailInfo($emailInfo);
			$mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId));
			$mailer->setStoreId($storeId);
			$mailer->setTemplateId("indicator_coupon");
			$mailer->setTemplateParams(array(
					'indicatorCoupon' => $indicatorCoupon,
					'indicatorLink'   => $indicatorLink
				)
			);

			$mailer->send();
			$this->helper->log("Email enviado para o indicador com o cupom");
		}
		catch (Mage_Core_Exception $e) {
			$this->helper->log($e->getMessage());
		}
		catch (Exception $e) {
			$this->helper->log($e->getMessage());
		}
    }

    /**
    * Retorna o nome do indicador
    *
    * @return null
    */
    public function getIndicatorName($indicatorEmail)
    {
        $customer = Mage::getModel("customer/customer")->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($indicatorEmail);
        if(strlen($customer->getName()) > 0){
            return $customer->getName();
        }else{
            return "sem nome";
        }
    }
}