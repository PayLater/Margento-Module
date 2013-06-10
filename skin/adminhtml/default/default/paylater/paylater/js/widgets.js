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

function PayLaterWidgetField (key, value) {
	var self = this;
	self.key = key;
	self.value = ko.observable(value);
}

function PayLaterWidget (name, fields) {
	var self = this;
	self.name = name;
	_ret = [];
	for (k in fields) {
		_ret.push(new PayLaterWidgetField(k, fields[k]));
	}
	self.fields = ko.observableArray(_ret);
}


var PayLaterWidgetsModel = Class.create();
PayLaterWidgetsModel.prototype = {
	initialize: function(jsonSourceUrl, isProductDefault, isCartDefault, isCheckoutDefault){
		var o = this;
		this.jsonSourceUrl = jsonSourceUrl;
		this.defaultData = false;
		this.initKoExtenders(o);
		this.currentWidget = ko.observable();
		this.widgetFields = {};
		this.widgets = ko.observableArray();
		// init calls
		if(!PAYLATER_WIDGETS_DEBUG){
			this.hideConfigTextareaRows();
		}
		this.getFieldsetTableElement();
		this.widgetsJSON = ko.observable();
		this.widgetOptions = ko.computed(function(){
			var opt = [];
			var parsed = ko.utils.parseJson(o.widgetsJSON());
			for(var k in parsed){
				opt.push(k);
				o.widgets.push(new PayLaterWidget(k, parsed[k]))
			};
			return opt;
		}).extend({logChange: "widgetOptions"});
		
		this.selectedWidget = ko.observable().extend({logChange: "selectedWidget"});
		
		this.configPanelHtml = ko.computed(function(){
			if(o.widgets().length){
				var selectedWidget = o.selectedWidget();
				for(var i = 0; i < o.widgets().length; i++){
					if(o.widgets()[i].name == selectedWidget){
						o.currentWidget(o.widgets()[i]);
						return o.widgets()[i].fields();
					}
				}
			}
			return {};
		});
		
		this.productConfig = ko.computed(function(){
			return '';
		});
		this.cartConfig = ko.computed(function(){
			return '';
		});
		this.checkoutConfig = ko.computed(function(){
			return '';
		});
		
		this.widgetSelection = ko.observable().extend({logChange: "widgetSelection"});
		// getting default JSON
		this.getDefaultJSON();
	},
	
	initKoExtenders: function (o) {
		ko.extenders.logChange = function(target, option) {
			if (PAYLATER_WIDGETS_DEBUG) {
				target.subscribe(function(newValue) {
					console.log(option + ": " + newValue);
				});
			}
			return target;
		};
	},
	hideConfigTextareaRows: function () {
		this.getConfigTextarea('product').hide();
		this.getConfigTextarea('cart').hide();
		this.getConfigTextarea('checkout').hide();
	},
	
	getConfigTextarea: function (type) {
		var id = 'row_paylater_widgets_' + type +  '_config';
		return $(id)
	},
	
	getDefaultJSON: function () {
		var parameters = {};
		var o = this;
		new Ajax.Request(this.jsonSourceUrl, {
			method: 'post',
			onSuccess: function(transport)    {
				if(transport.status == 200)    {
					o.widgetsJSON(transport.responseText);
				}
			},
			parameters: parameters
		});
	},
	
	getFieldsetTableElement: function () {
		$('paylater_widgets').down('table.form-list');
	},
	
	getDefaultData: function () {
		return this.defaultData;
	}
}