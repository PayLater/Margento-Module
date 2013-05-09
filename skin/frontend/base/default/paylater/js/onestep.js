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
var globalPaymentMethod;

var do_save_billing = function(url)    {
	var form = $('onestepcheckout-form');
	var items = exclude_unchecked_checkboxes($$('input[name^=billing]').concat($$('select[name^=billing]')));
	var names = items.pluck('name');
	var values = items.pluck('value');
	var parameters = {
		shipping_method: $RF(form, 'shipping_method')
	};


	var street_count = 0;
	for(var x=0; x < names.length; x++)    {
		if(names[x] != 'payment[method]')    {

			var current_name = names[x];

			if(names[x] == 'billing[street][]')    {
				current_name = 'billing[street][' + street_count + ']';
				street_count = street_count + 1;
			}

			parameters[current_name] = values[x];
		}
	}

	var use_for_shipping = $('billing:use_for_shipping_yes');

	if(use_for_shipping && use_for_shipping.getValue() != '1')    {
		var items = $$('input[name^=shipping]').concat($$('select[name^=shipping]'));
		var shipping_names = items.pluck('name');
		var shipping_values = items.pluck('value');
		var shipping_parameters = {};
		var street_count = 0;

		for(var x=0; x < shipping_names.length; x++)    {
			if(shipping_names[x] != 'shipping_method')    {
				var current_name = shipping_names[x];
				if(shipping_names[x] == 'shipping[street][]')    {
					current_name = 'shipping[street][' + street_count + ']';
					street_count = street_count + 1;
				}

				parameters[current_name] = shipping_values[x];
			}
		}
	}
	
	var payment_method = $RF(form, 'payment[method]');
		globalPaymentMethod = payment_method;
		parameters['payment_method'] = payment_method;
		parameters['payment[method]'] = payment_method;
		
	new Ajax.Request(url, {
		method: 'post',
		onSuccess: function(transport)    {
			if(transport.status == 200)    {

				$('PayLaterSubmit').submit();
			}
		},
		parameters: parameters
	});
}
function get_save_billing_function(url, set_methods_url, update_payments, triggered)
{
	if(typeof update_payments == 'undefined')    {
		var update_payments = false;
	}

	if(typeof triggered == 'undefined')    {
		var triggered = true;
	}

	if(!triggered){
		return function(){
			return;
		};
	}

	return function()    {
		
		var form = $('onestepcheckout-form');
		var items = exclude_unchecked_checkboxes($$('input[name^=billing]').concat($$('select[name^=billing]')));
		var names = items.pluck('name');
		var values = items.pluck('value');
		var parameters = {
			shipping_method: $RF(form, 'shipping_method')
		};


		var street_count = 0;
		for(var x=0; x < names.length; x++)    {
			if(names[x] != 'payment[method]')    {

				var current_name = names[x];

				if(names[x] == 'billing[street][]')    {
					current_name = 'billing[street][' + street_count + ']';
					street_count = street_count + 1;
				}

				parameters[current_name] = values[x];
			}
		}

		var use_for_shipping = $('billing:use_for_shipping_yes');




		if(use_for_shipping && use_for_shipping.getValue() != '1')    {
			var items = $$('input[name^=shipping]').concat($$('select[name^=shipping]'));
			var shipping_names = items.pluck('name');
			var shipping_values = items.pluck('value');
			var shipping_parameters = {};
			var street_count = 0;

			for(var x=0; x < shipping_names.length; x++)    {
				if(shipping_names[x] != 'shipping_method')    {
					var current_name = shipping_names[x];
					if(shipping_names[x] == 'shipping[street][]')    {
						current_name = 'shipping[street][' + street_count + ']';
						street_count = street_count + 1;
					}

					parameters[current_name] = shipping_values[x];
				}
			}
		}

		var shipment_methods = $$('div.onestepcheckout-shipping-method-block')[0];
		var shipment_methods_found = false;

		if(typeof shipment_methods != 'undefined') {
			shipment_methods_found = true;
		}

		if(shipment_methods_found)  {
			shipment_methods.update('<div class="loading-ajax">&nbsp;</div>');
		}

		var payment_method = $RF(form, 'payment[method]');
		globalPaymentMethod = payment_method;
		parameters['payment_method'] = payment_method;
		parameters['payment[method]'] = payment_method;
		var totals = get_totals_element();
		totals.update('<div class="loading-ajax">&nbsp;</div>');
		
		var payment_methods = $$('div.payment-methods')[0];
		payment_methods.update('<div class="loading-ajax">&nbsp;</div>');
			
		if (payment_method == 'paylater') {
			if ($$('div.onestepcheckout-place-order-wrapper')[0]) {
				$$('div.onestepcheckout-place-order-wrapper')[0].hide();
			}
		}
		new Ajax.Request(url, {
			method: 'post',
			onSuccess: function(transport)    {
				if(transport.status == 200)    {

					var data = transport.responseText.evalJSON();

					// Update shipment methods
					if(shipment_methods_found)  {
						shipment_methods.update(data.shipping_method);
					}
					payment_methods.replace(data.payment_method);
					if (globalPaymentMethod == 'paylater') {
						totals.update('<div class="loading-ajax">&nbsp;</div>');
					} else {
						totals.update(data.summary);
					}
					// Add new event handlers

					if(shipment_methods_found)  {
						$$('dl.shipment-methods input').invoke('observe', 'click', get_separate_save_methods_function(set_methods_url, update_payments));
					}

					$$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', get_separate_save_methods_function(set_methods_url));

					$$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', function() {
						$$('div.onestepcheckout-payment-method-error').each(function(item) {
							new Effect.Fade(item);
						});
					});

					if(shipment_methods_found)  {
						$$('dl.shipment-methods input').invoke('observe', 'click', function() {
							$$('div.onestepcheckout-shipment-method-error').each(function(item) {
								new Effect.Fade(item);
							});
						});
					}

					if($RF(form, 'payment[method]') != null)    {
						try    {
							var payment_method = $RF(form, 'payment[method]');
							$('container_payment_method_' + payment_method).show();
							$('payment_form_' + payment_method).show();
						} catch(err)    {

						}
					}
					
					if (globalPaymentMethod == 'paylater') {
						if ($$('div.onestepcheckout-place-order-wrapper')[0]) {
							$$('div.onestepcheckout-place-order-wrapper')[0].hide();
						}

						new Ajax.Request(url.split('/')[0] + '//' + window.location.host + '/paylater/onestep/review', {
							method: 'post',
							onSuccess: function(transport)    {
								if(transport.status == 200)    {
									totals.update(transport.responseText);
									if ($$('div.onestepcheckout-place-order-wrapper')[0]) {
										$$('div.onestepcheckout-place-order-wrapper')[0].hide();
								}
								}
							},
							parameters: parameters
						});
			
					}

				}
			},
			parameters: parameters
		});
		
		
		
	}
}

function get_separate_save_methods_function(url, update_payments)
{
	if(typeof update_payments == 'undefined')    {
		var update_payments = false;
	}

	return function(e)    {
		if(typeof e != 'undefined')    {
			var element = e.element();

			if(element.name != 'shipping_method')    {
				update_payments = false;
			}
		}
			var form = $('onestepcheckout-form');
			var shipping_method = $RF(form, 'shipping_method');
			var payment_method = $RF(form, 'payment[method]');
			globalPaymentMethod = payment_method;
			var totals = get_totals_element();
			var freeMethod = $('p_method_free');
			if(freeMethod){
				payment.reloadcallback = true;
				payment.countreload = 1;
			}
			totals.update('<div class="loading-ajax">&nbsp;</div>');

			if(update_payments)    {
				var payment_methods = $$('div.payment-methods')[0];
				payment_methods.update('<div class="loading-ajax">&nbsp;</div>');
			}

			var parameters = {
				shipping_method: shipping_method,
				payment_method: payment_method
			}

			/* Find payment parameters and include */
			var items = $$('input[name^=payment]').concat($$('select[name^=payment]'));
			var names = items.pluck('name');
			var values = items.pluck('value');

			for(var x=0; x < names.length; x++)    {
				if(names[x] != 'payment[method]')    {
					parameters[names[x]] = values[x];
				}
			}
			if (payment_method == 'paylater') {
				if ($$('div.onestepcheckout-place-order-wrapper')[0]) {
					$$('div.onestepcheckout-place-order-wrapper')[0].hide();
				}
			}
			
			new Ajax.Request(url, {
				method: 'post',
				onSuccess: function(transport)    {
					if(transport.status == 200)    {
						var data = transport.responseText.evalJSON();
						var form = $('onestepcheckout-form');
						if (globalPaymentMethod == 'paylater') {
							totals.update('<div class="loading-ajax">&nbsp;</div>');
						} else {
							totals.update(data.summary);
						}
					
						$$('div.onestepcheckout-place-order-wrapper')[0].show();
						if(update_payments)    {

							payment_methods.replace(data.payment_method);

							$$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', get_separate_save_methods_function(url));
							$$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', function() {
								$$('div.onestepcheckout-payment-method-error').each(function(item) {
									new Effect.Fade(item);
								});
							});

							if($RF($('onestepcheckout-form'), 'payment[method]') != null)    {
								try    {
									var payment_method = $RF(form, 'payment[method]');
									$('container_payment_method_' + payment_method).show();
									$('payment_form_' + payment_method).show();
								} catch(err)    {

								}
							}
						}
						
						if (globalPaymentMethod == 'paylater') {

							if ($$('div.onestepcheckout-place-order-wrapper')[0]) {
									$$('div.onestepcheckout-place-order-wrapper')[0].hide();
							}
							new Ajax.Request(url.split('/')[0] + '//' + window.location.host + '/paylater/onestep/review', {
								method: 'post',
								onSuccess: function(transport)    {
									if(transport.status == 200)    {
										totals.update(transport.responseText);
										if ($$('div.onestepcheckout-place-order-wrapper')[0]) {
											$$('div.onestepcheckout-place-order-wrapper')[0].hide();
										}
									}
								},
								parameters: parameters
							});
			
						}
					}
				},
				parameters: parameters
			});
		}
	}

