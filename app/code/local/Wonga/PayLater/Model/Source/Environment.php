<?php
/**
 * Wonga.com Ltd extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 * 
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Wonga PayLater module to newer versions in the future.
 * If you wish to customize the Wonga PayLater module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Wonga
 * @package    Wonga_PayLater
 * @copyright  Copyright (C) 2012 Wonga.com Ltd (http://www.wonga.com/)
 * @license    
 */

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Wonga
 * @package    Wonga_PayLater
 * @subpackage Model
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class Wonga_PayLater_Model_Source_Environment extends Mage_Core_Model_Abstract implements Wonga_PayLater_Model_Interface_PayLater
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
