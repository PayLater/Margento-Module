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
Product.OptionsPrice.prototype.formatPrice = function (price) {
	var formattedCurrency = formatCurrency(price, this.priceFormat);
	this.setPayLaterOffer(price);
	return formattedCurrency;
}

Product.OptionsPrice.prototype.setPayLaterOffer = function (price) {
	if ($(this.containers[0]) && typeof $$('div.product-essential p.special-price')[0] != 'undefined') {
		var specialPrice = $(this.containers[0]).innerHTML.trim();
		price = parseFloat(specialPrice.replace(/[^0-9-.]/g, ''));
	}
	if (typeof PayLater != 'undefined') {
		if (PayLater.isTestMode) {
			offer = PayLater.getOffer(price, offer.merchant, 'test');
		} else {
			offer = PayLater.getOffer(price, offer.merchant);
		}
		if (price > offerUpperBound) {
			$('representative-holder').hide();
			$('representative-pop').update('');
			$('representative-outrange-notice').show();
			return;
		} else {
			$('representative-holder').show();
			$('representative-outrange-notice').hide();
		}
		var representativePop = PayLater.GetFullInfo(offer, representativeLegal);
		if ($('representative-pop')) {
			$('representative-pop').update(representativePop);
			if ($('PayLater-greyBackground')) {
				document.getElementById('PayLater-greyBackground').onclick = function () {
				     document.getElementById('PayLater-fullInfo').style.display = "none";
				     document.getElementById('PayLater-greyBackground').style.display = "none";
			 	};
			}
			
			if (document.getElementById('PayLater-fullInfo')) {
				document.getElementById('Popup-inner-1-link').onclick = function (e) {
					document.getElementById('Popup-inner-1').style.display = "none";
					document.getElementById('Popup-inner-2').style.display = "block";
					e.preventDefault();
				};

				document.getElementById('Popup-inner-2-link').onclick = function (e) {
					document.getElementById('Popup-inner-1').style.display = "block";
					document.getElementById('Popup-inner-2').style.display = "none";
					e.preventDefault();
				};
			}
		}
	}
}

