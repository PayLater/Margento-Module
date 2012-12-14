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
class PayLater_PayLater_Model_Payment_Gateway extends Mage_Payment_Model_Method_Abstract implements PayLater_PayLater_Core_Interface
{
	/**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'paylater';
	protected $_isGateway = true;
	protected $_canCapture = true;
    protected $_canCapturePartial = true;
	protected $_formBlockType = 'paylater/payment_form';
	protected $_infoBlockType = 'paylater/payment_info';

	
	/**
     * Return Order place redirect url as boolean
     *
     * @return boolean
     */
    public function getOrderPlaceRedirectUrl()
    {
          return true;
    }
}
