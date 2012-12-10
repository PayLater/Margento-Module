<?php
/**
 * PayLater extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the PayLater PayLater module to newer versions in the future.
 * If you wish to customize the PayLater PayLater module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @copyright  Copyright (C) 2012 PayLater
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Block
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_CheckoutController extends Mage_Core_Controller_Front_Action implements PayLater_PayLater_Core_Interface
{

	protected function _getOnepage()
	{
		return Mage::getModel('paylater/checkout_onepage')->getSingleton();
	}
	
	protected function _collectAllItems ()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		$items = $quote->getAllItems();
		$all = array();
		foreach ($items as $item) {
			$arrayItem = array();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_KEY] = $item->getSku();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_DESCRIPTION_KEY] = $item->getDescription();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_QTY_KEY] = $item->getQty();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_PRICE_KEY] = $item->getPrice();
			$all[] = $arrayItem;
		}
		return $all;
	}
	/**
	 * @deprecated
	 * @return string
	 */
	protected function _outputPayLaterFormBlock()
	{
		$layout = $this->getLayout();
        $update = $layout->getUpdate();
		$update->load('paylater_checkout_form');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
	}
	
	/**
	 * Saves order and sets its status and state to PayLater Orphaned 
	 */
	public function gatewayAction()
	{
		$onepage = $this->_getOnepage();
		$quote = $onepage->getQuote();
		$quote->collectTotals()->save();
		$paylaterData = $this->getRequest()->getPost();
		try {
			// stops sending order email for order in saveOrder
			if ($onepage->saveOrder()) {
				try {
					$orderId = $quote->getReservedOrderId();
					$order = Mage::getModel('paylater/checkout_onepage')->getOrder($orderId);
					$order->setState(self::PAYLATER_ORPHANED_ORDER_STATE);
					$order->setStatus(self::PAYLATER_ORPHANED_ORDER_STATUS);
					$order->save();
					$paylaterData[self::PAYLATER_PARAMS_MAP_ORDERID_KEY] = $orderId;
					$this->loadLayout();
					$this->_initLayoutMessages('customer/session');
					$this->getLayout()->getBlock('head')->setTitle($this->__('PayLater Processing...'));
					$this->renderLayout();
					//$this->getResponse()->setBody($this->_outputPayLaterFormBlock());
				} catch (PayLater_PayLater_Exception_InvalidHttpClientResponse $e) {
					echo $e->getMessage();
				} catch (Mage_Core_Exception $e) {
					echo $e->getMessage();
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}
		} catch (Mage_Core_Exception $e) {
			echo $e->getMessage();
		}
	}
}