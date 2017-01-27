<?php
/**
* 
*/
class Cammino_Affiliateclub_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function __construct()
    {
        $this->enableLog = true;
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
        $this->log("Existe email do indicador: " . $_GET["indicatorEmail"]);
        return $_GET["indicatorEmail"];
    }

    /**
    * Retorna o código do cupom do indicado que esta na url
    *
    * @return string
    */
    public function getIndicatedCoupon()
    {
        $this->log("Existe cupom desconto para o indicado: " . $_GET["indicatedCoupon"]);
        return $_GET["indicatedCoupon"];
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
    public function log($message)
    {
        if($this->enableLog){
            Mage::log($message , null, "affiliateclub.log");
        }
    }
}