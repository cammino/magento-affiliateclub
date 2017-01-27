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
        if($this->helper->existsIndicatorEmailInUrl())
        {
            $this->helper->setIndicatorEmailInSession();

            if($this->helper->existsIndicatedCouponInUrl())
            {
                if($this->model->applyCoupon($this->helper->getIndicatedCoupon()))
                {
                    Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
                }
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
        $order = $observer->getEvent()->getInvoice()->getOrder();

        $collection = mage::getModel('affiliateclub/affiliateclub')
            ->getCollection()
            ->addFieldToFilter('order_id', $order->getId());

        if($collection->getSize() > 0){
            $this->helper->log("Pedido #" . $order->getId() . " possui um indicador");
            $affiliateclub = $collection->getFirstItem();

            $indicatorEmail = $affiliateclub->getIndicatorEmail();
            $indicatorName  = $this->model->getIndicatorName();
            $indicatorCoupon = $this->model->generateCoupon();

            $this->model->saveIndicatorCoupon($order, $indicatorCoupon); 
            $this->model->sendEmailIndicatorCoupon($indicatorName, $indicatorEmail, $indicatorCoupon);
        }
    }

    /**
    * Observer executado quando um pedido é criado
    *
    * @return null
    */
    public function checkOrderCreated(Varien_Event_Observer $observer)
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