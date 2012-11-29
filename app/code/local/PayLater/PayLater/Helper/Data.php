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
class PayLater_PayLater_Helper_Data extends Mage_Core_Helper_Data implements PayLater_PayLater_Model_Interface_PayLater
{
	/**
	 *
	 * @var GPMD_Data_Inflector 
	 */
	protected $_inflector;
	
	/**
	 *  Gets store config value for node and key passed as argument.
	 * 
	 * @param string $suiteModule
	 * @param string $node
	 * @param string $key
	 * @return mixed 
	 */
	protected function _getModuleConfig($node, $key)
	{
		return Mage::getStoreConfig($this->getModuleName() . '/' . $node . '/' . $key, $this->getStoreId());
	}
	
	
	/**
	 * Returns system log status
	 * 
	 * @return int 
	 */
	protected function _getSystemLogStatus()
	{
		return Mage::getStoreConfig(self::XML_NODE_SYSTEM_DEV_LOG_ACTIVE, $this->getStoreId());
	}
	
	
	/**
	 * Magic method __call handles methods starting with:
	 * 
	 * getPayLaterConfig[CAMELCASED CONFIG PATH]([CONFIG NODE])
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return mixed 
	 */
	public function __call($name, $arguments)
	{
		if (preg_match('/getPayLaterConfig\w/', $name)) {
			$name = str_replace('getPayLaterConfig', '', $name);
			$inflectedName = $this->getInflector()->underscore($name);
			return $this->_getModuleConfig($arguments[0], $inflectedName);
		}
	}
	
	
	/**
	 * Returns helper's module name in format modulename
	 * 
	 * @return string 
	 */
	public function getModuleName()
	{
		$name = $this->_getModuleName();
		list($company, $module) = explode('_', $name);
		return strtolower(implode('_', array($module)));
	}
	
	/**
	 *
	 * @return Wonga_PayLater_Helper_Inflector 
	 */
	public function getInflector()
	{
		if (!isset($this->_inflector)) {
			$this->_inflector = new PayLater_PayLater_Helper_Inflector();
		}
		return $this->_inflector;
	}
	
	/**
	 * Returns value for config node passed as argument,
	 * or false otherwise.
	 * 
	 * @param string $node
	 * @return mixed 
	 */
	public function getConfigNode ($node)
	{
		return Mage::getConfig()->getNode($node);
	}

}
