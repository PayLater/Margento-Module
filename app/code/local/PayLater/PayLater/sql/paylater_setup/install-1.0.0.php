<?php

$installer = $this;
/* @var $installer GPMD_Paylater_Model_Resource_Setup */

$installer->startSetup();

$configValuesMap = array(
	PayLater_PayLater_Core_Interface::XML_PATH_PAYLATER_EMAIL_TEMPLATE => PayLater_PayLater_Core_Interface::XML_PATH_PAYLATER_EMAIL_TEMPLATE_NODE,
	PayLater_PayLater_Core_Interface::XML_PATH_PAYLATER_EMAIL_GUEST_TEMPLATE => PayLater_PayLater_Core_Interface::XML_PATH_PAYLATER_EMAIL_GUEST_TEMPLATE_NODE
);

foreach ($configValuesMap as $configPath => $configValue) {
	$installer->setConfigData($configPath, $configValue);
}

$installer->run("
    CREATE TABLE IF NOT EXISTS `{$installer->getTable('paylater/refund')}` (
      `refund_id` int(11) NOT NULL auto_increment,
      `merchant_reference` varchar(20) NOT NULL,
      `order_id` varchar(50) NOT NULL,
	  `currency` varchar(5) NOT NULL,
	  `refund_value` decimal(12,4) NOT NULL,
	  `reason_code` varchar(10) NOT NULL,
      `export_date` datetime default NULL,
      `refund_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
      PRIMARY KEY  (`refund_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
       
");
$installer->endSetup();

