<?php
/**
* 
*/
class Cammino_Affiliateclub_Model_Observer extends Varien_Object
{
    public function __construct()
    {
        $this->helper = Mage::helper('affiliateclub');
        $this->model = Mage::getModel('affiliateclub/affiliateclub');
    }

    /**
    * Observer executado no carregamento de todas as páginas
    *
    * @return null
    */
    public function checkUrl(Varien_Event_Observer $observer)
    {
        if($this->helper->isModuleEnable())
        {
            if($this->helper->existsIndicatorEmailInUrl())
            {
                $this->helper->setIndicatorEmailInSession();

                if($this->helper->existsIndicatedCouponInUrl() && $this->model->setCoupon($this->helper->getIndicatedCoupon()))
                {
                    Mage::app()->getResponse()->setRedirect($this->helper->getRedirectPage());
                }
            }

            if($this->helper->existsIndicatorCouponInUrl() && $this->model->setCoupon($this->helper->getIndicatorCoupon()))
            {
                Mage::app()->getResponse()->setRedirect($this->helper->getRedirectPage());
            }
        }
    }

    /**
    * Observer executado quando um pedido é faturado
    *
    * @return null
    */
    public function checkOrderInvoiced(Varien_Event_Observer $observer)
    {
        if($this->helper->isModuleEnable())
        {
            $order = $observer->getEvent()->getInvoice()->getOrder();

            $collection = mage::getModel('affiliateclub/affiliateclub')
                ->getCollection()
                ->addFieldToFilter('order_id', $order->getId());

            if($collection->getSize() > 0)
            {
                $this->helper->log("Pedido #" . $order->getId() . " possui um indicador");
                $affiliateclub = $collection->getFirstItem();

                $indicatorName  = $this->model->getIndicatorName();
                $indicatorEmail = $affiliateclub->getIndicatorEmail();
                $indicatedEmail = $affiliateclub->getIndicatedEmail();

                // Só gera um cupom de desconto único se a pessoa não indicou ela mesma.
                if($indicatorEmail != $indicatedEmail)
                {
                    $indicatorCoupon = $this->model->generateCoupon();
                    $this->model->sendEmailIndicatorCoupon($indicatorName, $indicatorEmail, $indicatorCoupon);
                }else{
                    $indicatorCoupon = "cupom inválido";
                }

                $this->model->saveIndicatorCoupon($order, $indicatorCoupon); 
            }
        }
    }

    /**
    * Observer executado quando um pedido é criado
    *
    * @return null
    */
    public function checkOrderCreated(Varien_Event_Observer $observer)
    {
        if($this->helper->isModuleEnable())
        {
            $incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($incrementId);

            $indicatorEmail = $this->helper->getIndicatorEmailInSession();

            // Se esse pedido possui um indicador, salva na tabela affiliateclub
            if(strlen($indicatorEmail) > 0)
            {
                $this->model->saveAffiliateOrder($order, $indicatorEmail);
            }
        }
    }

    public function customerLogin(Varien_Event_Observer $observer)
    {
        $core = Mage::getSingleton('core/session');
        if($core->getAffiliateclubLogin() != NULL){
            $core->unsAffiliateclubLogin();
            $session = Mage::getSingleton('customer/session');
            $session->setAfterAuthUrl(Mage::getBaseUrl() . "affiliateclub/indique");
            $session->setBeforeAuthUrl('');
        }
    }

    public function applyCoupon(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('core/session');
        $couponCode = (string)$session->getCustomCouponCode();

        if (!$couponCode or !strlen($couponCode)) {
            return;
        }

        $session = Mage::getSingleton('checkout/session');
        $cart = Mage::getSingleton('checkout/cart')->getQuote();
        $cart->getShippingAddress()->setCollectShippingRates(true);
        $cart->setCouponCode(strlen($couponCode) ? $couponCode : '')->collectTotals()->save();
        $this->helper->log("Aplicou o cupom: " . $couponCode);
    }
}