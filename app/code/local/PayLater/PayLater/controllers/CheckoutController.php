<?php 
class PayLater_PayLater_CheckoutController extends Mage_Core_Controller_Front_Action implements PayLater_PayLater_Core_Interface
{
	public function saveOrderAction ()
	{
		$onepage = $this->_getOnepage();
		$quote = $onepage->getQuote();
		$quote->collectTotals()->save();
		if ($onepage->saveOrder()) {
			$orderId = $quote->getReservedOrderId();
			$order = Mage::getModel('paylater/checkout_onepage')->getOrder($orderId);
			$order->setState(self::PAYLATER_ORPHANED_ORDER_STATE);
			$order->setStatus(self::PAYLATER_ORPHANED_ORDER_STATUS);
			$order->save();
		}
		return;
	}
	
	protected function _getOnepage()
	{
		return Mage::getModel('paylater/checkout_onepage')->getSingleton();
	}
}