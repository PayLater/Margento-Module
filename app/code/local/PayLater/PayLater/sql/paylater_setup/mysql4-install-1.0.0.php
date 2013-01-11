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

$installer->run("
    INSERT INTO  `{$this->getTable('sales/order_status')}` (
        `status` ,
        `label`
    ) VALUES (
        'paylater_orphaned',  'PayLater Orphaned'
    );
	INSERT INTO  `{$this->getTable('sales/order_status')}` (
        `status` ,
        `label`
    ) VALUES (
        'paylater_failed',  'PayLater Failed'
    );
    INSERT INTO  `{$this->getTable('sales/order_status_state')}` (
        `status` ,
        `state` ,
        `is_default`
    ) VALUES (
        'status_code',  'processing',  '0'
    );
	INSERT INTO  `{$this->getTable('sales/order_status_state')}` (
        `status` ,
        `state` ,
        `is_default`
    ) VALUES (
        'paylater_orphaned',  'pending_payment',  '0'
    );
	INSERT INTO  `{$this->getTable('sales/order_status_state')}` (
        `status` ,
        `state` ,
        `is_default`
    ) VALUES (
        'paylater_failed',  'canceled',  '0'
    );
");

$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_ORDER_STATUS, array('type'=>'varchar'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_INFO_TEXT, array('type'=>'varchar'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_EMAIL_INFO_TEXT, array('type'=>'varchar'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_AMOUNT, array('type'=>'decimal'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_FEE_PRICE, array('type'=>'decimal'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_INSTALLMENTS_AMOUNT, array('type'=>'decimal'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_AGREEMENT_DURATION_DAYS, array('type'=>'integer'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_APR, array('type'=>'varchar'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_TOTAL_TO_BE_PAID, array('type'=>'decimal'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_ADDITIONAL, array('type'=>'text'));

$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_ORDER_STATUS, array('type'=>'varchar'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_INFO_TEXT, array('type'=>'varchar'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_EMAIL_INFO_TEXT, array('type'=>'varchar'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_AMOUNT, array('type'=>'decimal'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_FEE_PRICE, array('type'=>'decimal'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_INSTALLMENTS_AMOUNT, array('type'=>'decimal'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_AGREEMENT_DURATION_DAYS, array('type'=>'integer'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_APR, array('type'=>'varchar'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_TOTAL_TO_BE_PAID, array('type'=>'decimal'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_ADDITIONAL, array('type'=>'text'));

$installer->endSetup();