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
	if (typeof value === 'boolean') {
		value = value.toString();
	}
	self.value = ko.observable(value);
}

function PayLaterWidget (name, fields) {
	var self = this;
	this.name = name;
	var _fields = [];
	for (k in fields) {
		_fields.push(new PayLaterWidgetField(k, fields[k]));
	}
	this.fields = ko.observableArray(_fields);
}

function PayLaterSystemWidget (name, fields) {
	var self = this;
	this.name = name;
	var _fields = [];
	for (k in fields) {
		_fields.push(new PayLaterWidgetField(k, fields[k]));
	}
	this.fields = ko.observableArray(_fields);
}


var PayLaterWidgetsModel = Class.create();
PayLaterWidgetsModel.prototype = {
	initialize: function(jsonSourceUrl, systemProductWidgetJSON, systemCartWidgetJSON, systemCheckoutWidgetJSON){
		var o = this;
		this.jsonSourceUrl = jsonSourceUrl;
		this.defaultData = false;
		this.initKoExtenders(o);
		this.widgetFields = {};
		this.widgets = ko.observableArray();
		// init calls
		if(!PAYLATER_WIDGETS_DEBUG){
			this.hideConfigTextareaRows();
		}
		this.getFieldsetTableElement();
		this.widgetsJSON = ko.observable();
		this.setWidgetOptions();
		
		this.selectedWidget = ko.observable();
		
		this.widgetSelection = ko.observable();
		
		this.isProductType = ko.computed(function(){
			return (o.widgetSelection() == 'product');
		});
		
		this.isCartType = ko.computed(function(){
			return (o.widgetSelection() == 'cart');
		});
		
		this.isCheckoutType = ko.computed(function(){
			return (o.widgetSelection() == 'checkout');
		});
		
		// getting default JSON
		this.getDefaultJSON();
		this.currentProductWidget = ko.observable().extend({
			logChange: "currentProductWidget"
		});
		this.currentCheckoutWidget = ko.observable().extend({
			logChange: "currentCheckoutWidget"
		});
		this.currentCartWidget = ko.observable().extend({
			logChange: "currentCartWidget"
		});
		
		this.configPanelFields = ko.computed(function(){
			if(o.widgets().length > 0 && o.widgetSelection() && typeof o.selectedWidget() != 'undefined'){

				var defaultSelectedWidget = o.getSelectedWidget();
			
				if (o.isProductType()) {
					o.currentProductWidget(defaultSelectedWidget);
					return defaultSelectedWidget.fields();
				} else if (o.isCartType()) {
					if (systemCartWidgetJSON) {
						var formattedWidget = o.formatSystemWidget(systemCartWidgetJSON);
						var parsed = ko.utils.parseJson(formattedWidget);
						o.prepareSystemWidgetObject(parsed);
						return o.currentCartWidget().fields();
					} else {
						o.currentCartWidget(defaultSelectedWidget);
						return defaultSelectedWidget.fields();
					}
				} else if (o.isCheckoutType()) {
					o.currentCheckoutWidget(defaultSelectedWidget);
					return defaultSelectedWidget.fields();
				} else {
					return defaultSelectedWidget.fields();
				}
			}
			return null;
		});
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
	setWidgetOptions: function () {
		var o = this;
		this.widgetOptions = ko.computed(function(){
			var opt = [];
			var parsed = ko.utils.parseJson(o.widgetsJSON());
			for(var k in parsed){
				opt.push(k);
				console.log(parsed[k]);
				o.widgets.push(new PayLaterWidget(k, parsed[k]));
			};
			return opt;
		});
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
	},
	
	getSelectedWidget: function () {
		var o = this;
		var _w;
		for (var i = 0; i < o.widgets().length; i++) {
			if ( o.widgets()[i].name == o.selectedWidget()) {
				_w = o.widgets()[i];
				break;
			}
		}
		return _w;
	},
	
	formatSystemWidget: function (widget) {
		var _format = {};
		_format[widget.name] = widget.fields;
		return ko.toJSON(_format);
	},
	
	prepareSystemWidgetObject: function (parsed) {
		var o = this;
		for (var i in parsed) {
			var _wName = i;
			var _wFields = {};
			for (var ii in parsed[i]) {
				if (typeof parsed[i][ii].key != 'undefined') {
					console.log(parsed[i][ii].key);
					var fKey = parsed[i][ii].key;
					var fValue = parsed[i][ii].value;
					_wFields[fKey] = fValue;
				}
			}
		}
		o.currentCartWidget(new PayLaterSystemWidget(_wName, _wFields));
	}
}