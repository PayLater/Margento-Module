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
 * @subpackage Model
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Model_Sales_Order implements PayLater_PayLater_Core_Interface
{
	protected $_order;
	
	protected function _getInstance()
	{
		return $this->_order;
	}
	
	public function __construct(array $arguments)
	{
		if (!array_key_exists(0, $arguments) && !(is_numeric($arguments[0]))) {
			throw new PayLater_PayLater_Exception_InvalidArguments(__METHOD__ . ' ' . Mage::helper('paylater')->__('Invalid arguments. First argument must be a valid Magento order increment id'));
		}
		$this->_order = Mage::getModel('sales/order')->load($arguments[0], 'increment_id');
	}
	
	public function setStateAndStatus()
	{
		$this->_getInstance()->setState(Mage_Sales_Model_Order::STATE_NEW);
		$this->_getInstance()->setStatus(Mage::helper('paylater')->getPayLaterConfigOrderStatus('payment'));
	}
	
	public function save()
	{
		$this->_getInstance()->save();
	}
	
	public function getInstance()
	{
		return $this->_getInstance();
	}
	
}
