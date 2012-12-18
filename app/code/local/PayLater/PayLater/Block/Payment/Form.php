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
class PayLater_PayLater_Block_Payment_Form extends Mage_Core_Block_Template implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_ShowableInterface, PayLater_PayLater_Core_TypeableInterface
{
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::PAYMENT_METHOD_FORM_TEMPLATE);
    }
	
	
	/**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShow() === false && Mage::helper('paylater')->isAllowedCurrency()) {
			$this->setTemplate(self::BASKET_NOT_IN_RANGE_TEMPLATE);
		}
		if (!$this->getTemplate()) {
            return '';
        }
        $html = $this->renderView();
        return $html;
    }
	
	/**
	 *
	 * @return float 
	 */
	public function getGrandTotal() 
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		return $quote->getGrandTotal();
	}
	/**
	 * @see PayLater_PayLater_Core_TypeableInterface
	 * @return string 
	 */
	public function getPayLaterType ()
	{
		return self::PAYLATER_TYPE_CHECKOUT;
	}
	/**
	 * @see PayLater_PayLater_Core_ShowableInterface
	 * @return boolean 
	 */
	public function canShow()
	{
		$helper = Mage::helper('paylater');
		return $helper->canShowAtCheckout() && $helper->isAllowedCurrency();
	}
}
