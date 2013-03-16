/**
 * PayLater extension for Magento
 *
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
 * @copyright  Copyright (C) 2013 PayLater
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
Payment.prototype.switchMethod = function (method) {
	if (this.currentMethod && $('payment_form_'+this.currentMethod)) {
		this.changeVisible(this.currentMethod, true);
		$('payment_form_'+this.currentMethod).fire('payment-method:switched-off', {
			method_code : this.currentMethod
			});
	}
	if ($('payment_form_'+method)){
		this.changeVisible(method, false);
		$('payment_form_'+method).fire('payment-method:switched', {
			method_code : method
		});
	} else {
		//Event fix for payment methods without form like "Check / Money order"
		document.body.fire('payment-method:switched', {
			method_code : method
		});
	}
	if ($('payment_form_paylater')) {
		$('payment_form_paylater').show();
	}
	if (method) {
		this.lastUsedMethod = method;
	}
	this.currentMethod = method;
}