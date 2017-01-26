<?php
/**
* 
*/
class Cammino_Affiliateclub_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function __construct()
    {
        $this->debugMode = true;
    }

    /**
    * Verifica se tem o email do indicador na URL
    *
    * @return boolean
    */
    public function existsIndicatorEmailInUrl()
    {
        if(isset($_GET["indicatorEmail"]) && strlen($_GET["indicatorEmail"]) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
    * Verifica se tem um cupom de desconto para o indicado na url
    *
    * @return boolean
    */
    public function existsIndicatedCouponInUrl()
    {
        if(isset($_GET["indicatedCoupon"]) && strlen($_GET["indicatedCoupon"]) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
    * Retorna o email do indicador que esta na url
    *
    * @return boolean
    */
    public function getIndicatorEmail()
    {
        $this->log("Existe email do indicador: " . $_GET["indicatorEmail"], true);
        return $_GET["indicatorEmail"];
    }

    /**
    * Retorna o código do cupom do indicado que esta na url
    *
    * @return string
    */
    public function getIndicatedCoupon()
    {
        $this->log("Existe cupom desconto para o indicado: " . $_GET["indicatedCoupon"], true);
        return $_GET["indicatedCoupon"];
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

            $this->log("Aplicou o cupom do indicado: " . $couponCode, true);
            return true;

        }catch (Mage_Core_Exception $e) {
            $this->log("Erro ao aplicar o cupom: " . $couponCode . ", Detalhes erro: " . $e);
        } catch (Exception $e) {
            $this->log("Erro ao aplicar o cupom: " . $couponCode . ", Detalhes erro: " . $e);
        }  
    }

    /**
    * Sava o email do indicador na sessão
    *
    * @return null
    */
    public function setIndicatorEmailInSession()
    {
        $email = $this->getIndicatorEmail();
        $_SESSION['affiliateclub_indicator_email'] = $email;
        $this->log("Salvou email " . $email . " na sessao", true);
        return true;
    }

    /**
    * Retorna o indicador que esta na sessão
    *
    * @return boolean
    */
    public function getIndicatorEmailInSession()
    {
        return $_SESSION['affiliateclub_indicator_email'];
    }

    /**
    * Exibe mensagem de log
    *
    * @return null
    */
    public function log($message, $debug = false)
    {
        if(($debug && $this->debugMode) || !$debug){
            Mage::log($message , null, "affiliateclub.log");
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
            $this->log($e->getMessage());
        }
    }
}