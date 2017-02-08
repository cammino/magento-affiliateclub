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
}