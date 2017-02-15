<?php
/**
* 
*/
class Cammino_Affiliateclub_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
    * Verifica se o módulo esta ativo no admin
    *
    * @return boolean
    */
    public function isModuleEnable()
    {
        return (bool) Mage::getStoreConfig('affiliateclub/affiliateclub_group/affiliateclub_active');
    }

    /**
    * Retorna o ID da regra de promoção usado para gerar os cupons de desconto
    *
    * @return int
    */
    public function getCouponRuleId()
    {
        return (int) Mage::getStoreConfig('affiliateclub/affiliateclub_group/affiliateclub_indicator_coupon_rule_id');
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
    * Verifica se tem um cupom de desconto para o indicador na url
    *
    * @return boolean
    */
    public function existsIndicatorCouponInUrl()
    {
        if(isset($_GET["indicatorCoupon"]) && strlen($_GET["indicatorCoupon"]) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
    * Retorna o email do indicador que esta na url
    *
    * @return String
    */
    public function getIndicatorEmail()
    {
        $this->log("---------------------------------");
        $this->log("Existe email do indicador: " . $_GET["indicatorEmail"]);
        return $_GET["indicatorEmail"];
    }

    /**
    * Retorna o código do cupom do indicado que esta na url
    *
    * @return String
    */
    public function getIndicatedCoupon()
    {
        $this->log("Existe cupom desconto para o indicado: " . $_GET["indicatedCoupon"]);
        return $_GET["indicatedCoupon"];
    }

    /**
    * Retorna o código do cupom do indicador que esta na url
    *
    * @return String
    */
    public function getIndicatorCoupon()
    {
        $this->log("---------------------------------");
        $this->log("Existe cupom desconto para o indicador: " . $_GET["indicatorCoupon"]);
        return $_GET["indicatorCoupon"];
    }

    /**
    * Retorna o código de desconto padrão do indicador que esta configurado no admin
    *
    * @return String
    */
    public function getDefaultIndicatedCoupon()
    {
        return Mage::getStoreConfig('affiliateclub/affiliateclub_group/affiliateclub_indicated_coupon');
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
        $this->log("Salvou email " . $email . " na sessao");
    }

    /**
    * Retorna o indicador que esta na sessão
    *
    * @return String
    */
    public function getIndicatorEmailInSession()
    {
        return $_SESSION['affiliateclub_indicator_email'];
    }

    /**
    * Retorna a url de compartilhamento
    *
    * @return String
    */
    public function getShareLink()
    {
        $coupon = Mage::getStoreConfig('affiliateclub/affiliateclub_group/affiliateclub_indicated_coupon');
        return Mage::getBaseUrl() . "?indicatorEmail=" . $this->getCustomerLoggedEmail() . "&indicatedCoupon=" . $coupon;
    }

    /**
    * Retorna a url que o indicado sera redirecionado após seu cupom ser aplicado
    *
    * @return String
    */
    public function getRedirectPage()
    {
        return Mage::getStoreConfig('affiliateclub/affiliateclub_group/affiliateclub_share_url');
    }

    /**
    * Retorna o email do usuario logado
    *
    * @return String
    */
    public function getCustomerLoggedEmail()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getSingleton('customer/session')->getCustomer()->getEmail();
        }else{
            return "";
        }
    }

    /**
    * Retorna o nome do usuario logado
    *
    * @return String
    */
    public function getCustomerLoggedName()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()){
            return Mage::getSingleton('customer/session')->getCustomer()->getName();
        }else{
            return "";
        }
    }

    /**
    * Exibe mensagem de log
    *
    * @return null
    */
    public function log($message)
    {
        $enable = (bool) Mage::getStoreConfig('affiliateclub/affiliateclub_group/affiliateclub_log_active');

        if($enable){
            Mage::log($message , null, "affiliateclub.log");
        }
    }
}