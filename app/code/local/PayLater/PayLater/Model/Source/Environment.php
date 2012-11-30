<?php
/**
 * PayLater extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 * [TO BE DEFINED]
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Wonga PayLater module to newer versions in the future.
 * If you wish to customize the PayLater module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @copyright  Copyright (C) 2012 PayLater
 * @license    [TO BE DEFINED]
 */

/**
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Helper
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Model_Source_Environment extends Mage_Core_Model_Abstract implements PayLater_PayLater_Core_Interface
{
	public function toOptionArray()
    {
        return array(
            array(
                'value' => self::ENVIRONMENT_TEST,
                'label' => Mage::helper('paylater')->__('Test')
            ),
            array(
                'value' => self::ENVIRONMENT_LIVE,
                'label' => Mage::helper('paylater')->__('Live')
            )
        );
    }
}
