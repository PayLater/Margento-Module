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
class PayLater_PayLater_Block_Checkout_Gateway extends Mage_Core_Block_Template implements PayLater_PayLater_Core_Interface
{
	
	protected function _collectAllItems ()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		$items = $quote->getAllItems();
		
		$all = array();
		foreach ($items as $item) {
			$arrayItem = array();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_KEY] = $item->getSku();
			if ($item->getDescription() && strlen($item->getDescription() < self::PAYLATER_PARAMS_MAP_ITEM_MAX_DESCRIPTION_LENGTH)) {
				$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_DESCRIPTION_KEY] = $item->getDescription();
			} elseif($item->getShortDescription() && strlen($item->getShortDescription() < self::PAYLATER_PARAMS_MAP_ITEM_MAX_DESCRIPTION_LENGTH)){
				$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_DESCRIPTION_KEY] = $item->getShortDescription();
			}else {
				$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_DESCRIPTION_KEY] = $item->getName();
			}
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_QTY_KEY] = $item->getQty();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_PRICE_KEY] = $item->getPrice();
			$all[] = $arrayItem;
		}
		return $all;
	}
	
	public function getActionUrl()
	{
		return self::PAYLATER_ENDPOINT_TEST;
	}
	
	public function collectPayLaterData()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		$paylaterData = $this->getRequest()->getPost();
		return array(
			self::PAYLATER_PARAMS_MAP_REFERENCE_KEY => $paylaterData[self::PAYLATER_PARAMS_MAP_REFERENCE_KEY],
			self::PAYLATER_PARAMS_MAP_AMOUNT_KEY  => $paylaterData[self::PAYLATER_PARAMS_MAP_AMOUNT_KEY],
			self::PAYLATER_PARAMS_MAP_ORDERID_KEY => $quote->getReservedOrderId(),
			self::PAYLATER_PARAMS_MAP_CURRENCY_KEY => $paylaterData[self::PAYLATER_PARAMS_MAP_CURRENCY_KEY],
			self::PAYLATER_PARAMS_MAP_POSTCODE_KEY => $paylaterData[self::PAYLATER_PARAMS_MAP_POSTCODE_KEY],
			self::PAYLATER_PARAMS_MAP_RETURN_LINK_KEY => Mage::getBaseUrl() . self::PAYLATER_PARAMS_MAP_RETURN_LINK,
			'item' => $this->_collectAllItems()
		);
	}
}
