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
class PayLater_PayLater_Block_Checkout_Onepage_Review_Info extends Mage_Checkout_Block_Onepage_Review_Info implements PayLater_PayLater_Core_ShowableInterface
{
	
	public function _construct()
	{
		parent::_construct();
	}
	
	/**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShow() === false) {
			$checkout = Mage::getModel('paylater/checkout_onepage')->getCheckout();
			$checkout->setStepData('review', 'allow', false);
			$checkout->setStepData('payment', 'allow', true);
			$this->setTemplate('paylater/paylater/service/unavailable.phtml');
			//return '';
		}
		if (!$this->getTemplate()) {
            return '';
        }
        $html = $this->renderView();
        return $html;
    }
	
	public function canShow()
	{
		return $this->helper('paylater')->canShowAtCheckout();
	}
}
