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
bindEvent = function(element, type, handler) {
	if (element.addEventListener) {
		return element.addEventListener(type, handler, false);
	} else {
		return element.attachEvent('on' + type, handler);
	}
};

loadScript = function(src, callback) {
	var s, t;

	s = document.createElement('script');
	s.type = 'text/javascript';
	s.async = 'true';
	s.src = src;
	s.onload = s.onreadystatechange = function() {
		var e, rs;

		rs = this.readyState;
		if (rs && rs !== 'complete' && rs !== 'loaded') {

		} else {
			try {
				return callback();
			} catch (_error) {
				e = _error;
				console.log(e.name + ": " + e.message);
			}
		}
	};
	t = document.getElementsByTagName('head')[0];
	t.appendChild(s);
};