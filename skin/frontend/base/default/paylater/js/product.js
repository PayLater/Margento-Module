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

    if (price >= offerLowerBound) {
        if (typeof PayLater != 'undefined') {
            if (price > offerUpperBound) {
                //remove widget
                $('paylater-widget-wrapper').update('<div class="paylater-out-of-range"><img src="http://paylater.s3.amazonaws.com/images/paylater-payment-type.png" height="21" width="50"/> <span class="relevant">PayLater</span> is only available for products with a price between &pound;' + offerLowerBound + ' and &pound;' + offerUpperBound + '</div>');
            }
            if (price >= offerLowerBound && price <= offerUpperBound) {
                // add widget
                if ($('paylater-widget-wrapper')) {
                    $('paylater-widget-wrapper').innerHTML = '<div id="paylater-widget-holder"></div>';
                    $('paylater-widget-holder').setAttribute('class', 'pl-widget');
                    $('paylater-widget-holder').setAttribute('data-pl-price', price);
                    $('paylater-widget-holder').setAttribute('data-pl-widget', plDataName);
                    $('paylater-widget-holder').setAttribute('data-pl-legal', plDataLegal);
                }

                if (plDataShowBreakDown) {
                    $('paylater-widget-holder').setAttribute('data-pl-showbreakdown', plDataShowBreakDown);
                }

                if (plDataParams != 'false') {
                    $('paylater-widget-holder').setAttribute('data-pl-params', plDataParams);
                }

            }

            if (price < offerLowerBound) {
                // remove widget
                $('paylater-widget-wrapper').update('<div class="paylater-out-of-range"><img src="http://paylater.s3.amazonaws.com/images/paylater-payment-type.png" height="21" width="50"/> <span class="relevant">PayLater</span> is only available for products with a price between &pound;' + offerLowerBound + ' and &pound;' + offerUpperBound + '</div>');
            }
            PayLaterWidget.refreshWidgets();
        }
    } else {
        // remove widget
        $('paylater-widget-wrapper') ? $('paylater-widget-wrapper').update('') : '';
    }


}

