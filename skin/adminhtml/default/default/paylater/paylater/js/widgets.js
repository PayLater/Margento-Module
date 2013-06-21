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

var PayLaterWidgetsModel = Class.create();
PayLaterWidgetsModel.prototype = {
	initialize: function(jsonSource, systemProductWidgetJSON, systemCartWidgetJSON, systemCheckoutWidgetJSON){
		var o = this;
        this._initKoExtenders(o);

        // loaded JSON
		this.jsonSource = ko.utils.parseJson(jsonSource);

        this.checkInherits = ko.observable(false);
        this.shouldShowDebugRow = ko.observable(false);

        this.systemProductWidgetJSON =  systemProductWidgetJSON;
        this.systemCartWidgetJSON =  systemCartWidgetJSON;
        this.systemCheckoutWidgetJSON =  systemCheckoutWidgetJSON;

        this.defaultProductWidget = ko.observable(this._getDefaultWidgetByName('price-offer'));
        this.defaultCartWidget = ko.observable(this._getDefaultWidgetByName('price-offer'));
        this.defaultCheckoutWidget = ko.observable(this._getDefaultWidgetByName('radio-button'));

        this.currentProductWidget = ko.observable();
        this.currentCheckoutWidget = ko.observable();
        this.currentCartWidget = ko.observable();

        this.selectedWidget = ko.observable();
        this.widgetSelection = ko.observable();

		if(!PAYLATER_WIDGETS_DEBUG){
			this._hideConfigTextareaRows();
		}
		this._getFieldsetTableElement();
		this._setTypeOptions();
		this._setWidgetOptions();

		this.showConfigPanel = ko.computed(function(){
			return o.selectedWidget() && o.widgetSelection();
		});
		
		this.isProductType = ko.computed(function(){
			return (o.widgetSelection() == 'Product');
		});
		
		this.isCartType = ko.computed(function(){
			return (o.widgetSelection() == 'Cart');
		});
		
		this.isCheckoutType = ko.computed(function(){
			return (o.widgetSelection() == 'Checkout');
		});



		this.configPanelFields = ko.computed(function(){
            o.checkInherits(false);
            if(!o.widgetSelection() && typeof o.selectedWidget() == 'undefined'){
                o._initTextareas();
            }
            if(o.widgetSelection() && typeof o.selectedWidget() == 'undefined'){
                o._autosetSelectedWidget();
            }
            if(o.widgetSelection() && typeof o.selectedWidget() != 'undefined'){

				var selectedWidget = o._getSelectedWidget();
			
				if (o.isProductType()) {
                    if (!o.systemProductWidgetJSON) {
                        var _nw = o._switchDefaultWidget(selectedWidget);
                        if (typeof _nw == 'object') {
                            return _nw;
                        }
                        return o._setProductWidget(o.defaultProductWidget());
                    }
					return o._setProductWidget(selectedWidget);
				} else if (o.isCartType()) {
                    if (!o.systemCartWidgetJSON) {
                        var _nw = o._switchDefaultWidget(selectedWidget);
                        if (typeof _nw== 'object') {
                            return _nw;
                        }
                        return o._setCartWidget(o.defaultCartWidget());
                    }
					return o._setCartWidget(selectedWidget);
				} else if (o.isCheckoutType()) {
                    if (!o.systemCheckoutWidgetJSON) {
                        var _nw = o._switchDefaultWidget(selectedWidget);
                        if (typeof _nw == 'object') {
                            return _nw;
                        }
                        return o._setCheckoutWidget(o.defaultCheckoutWidget());
                    }
					return o._setCheckoutWidget(selectedWidget);
				}
			}
			return null;
		});
	},

    _initTextareas : function () {
        var o = this;
        if (!o.systemProductWidgetJSON) {
            o._setProductWidget(o.defaultProductWidget());
        } else {
            o._setProductWidget(o._getSystemWidget('Product'));
        }
        if (!o.systemCartWidgetJSON) {
            o._setCartWidget(o.defaultCartWidget());
        } else {
            o._setCartWidget(o._getSystemWidget('Cart'));
        }
        if (!o.systemCheckoutWidgetJSON) {
            o._setCheckoutWidget(o.defaultCheckoutWidget());
        } else {
            o._setCheckoutWidget(o._getSystemWidget('Checkout'));
        }
    },

    _prepareWidgetFields: function (fields) {
        var _fields = {};
        for (var i in fields) {
           if (typeof fields[i].key != 'undefined') {
               _fields[fields[i].key] = fields[i].value;
           }
        }
        return _fields;
    },

    _getSystemWidget: function (type) {
        var o = this;
        var _w = false;
        switch (type) {
            case 'Product':
                _w = new PayLaterWidget(o.systemProductWidgetJSON.name, o._prepareWidgetFields(o.systemProductWidgetJSON.fields));
                break;
            case 'Cart':
                _w = new PayLaterWidget(o.systemCartWidgetJSON.name, o._prepareWidgetFields(o.systemCartWidgetJSON.fields));
                break;
            case 'Checkout':
                _w = new PayLaterWidget(o.systemCheckoutWidgetJSON.name, o._prepareWidgetFields(o.systemCheckoutWidgetJSON.fields));
                break;
        }
        return _w;
    },

    _autosetSelectedWidget: function () {
        var o = this;
        if (o.widgetSelection() == 'Product' && o.systemProductWidgetJSON) {
            o.selectedWidget(o.systemProductWidgetJSON.name);
        } else if(o.widgetSelection() == 'Product' && o.systemProductWidgetJSON == false) {
            o.selectedWidget('price-offer');
        }
        if (o.widgetSelection() == 'Cart' && o.systemCartWidgetJSON) {
            o.selectedWidget(o.systemCartWidgetJSON.name);
        } else if(o.widgetSelection() == 'Cart' && o.systemCartWidgetJSON == false) {
            o.selectedWidget('price-offer');
        }
        if (o.widgetSelection() == 'Checkout' && o.systemCheckoutWidgetJSON) {
            o.selectedWidget(o.systemCheckoutWidgetJSON.name);
        } else if(o.widgetSelection() == 'Checkout' && o.systemCheckoutWidgetJSON == false) {
            o.selectedWidget('radio-button');
        }
    },

    _switchDefaultWidget: function (selectedWidget) {
        var o = this;

        if (o.isProductType()) {
            if (selectedWidget.name != o.defaultProductWidget().name) {
                return o._setProductWidget(o._getDefaultWidgetByName(selectedWidget.name));
            }
        }
        if (o.isCartType()) {
            if (selectedWidget.name != o.defaultCartWidget().name) {
                return o._setCartWidget(o._getDefaultWidgetByName(selectedWidget.name));
            }
        }
        if (o.isCheckoutType()) {
            if (selectedWidget.name != o.defaultCheckoutWidget().name) {
                return o._setCheckoutWidget(o._getDefaultWidgetByName(selectedWidget.name));
            }
        }
        return false;
    },

    _getDefaultWidgetByName: function (name) {
        for (var i in this.jsonSource) {
           if (i == name) {
               return new PayLaterWidget(i, this.jsonSource[i]);
           }
        }
    },
	
	_initKoExtenders: function (o) {
		ko.extenders.logChange = function(target, option) {
			if (PAYLATER_WIDGETS_DEBUG) {
				target.subscribe(function(newValue) {
					console.log(option + ": " + newValue);
				});
			}
			return target;
		};
	},
	_setTypeOptions: function () {
		var o = this;
		this.typeOptions = ko.computed(function(){
			var opt = ['Product', 'Cart', 'Checkout'];
			return opt;
		});
	},
	_setWidgetOptions: function () {
		var o = this;
		this.widgetOptions = ko.computed(function(){
			var opt = [];
			for(var k in o.jsonSource){
				opt.push(k);
			};
			return opt;
		});
	},
	
	_hideConfigTextareaRows: function () {
		this._getConfigTextarea('product').hide();
		this._getConfigTextarea('cart').hide();
		this._getConfigTextarea('checkout').hide();
	},
	
	_getConfigTextarea: function (type) {
		var id = 'row_paylater_widgets_' + type +  '_config';
		return $(id)
	},
	
	_getFieldsetTableElement: function () {
		$('paylater_widgets').down('table.form-list');
	},

	_getSelectedWidget: function () {
		var o = this;
        if (o.widgetSelection() == 'Product' && o.systemProductWidgetJSON && o.systemProductWidgetJSON.name == o.selectedWidget()) {
            return o._getSystemWidget('Product');
        }
        if (o.widgetSelection() == 'Cart' && o.systemCartWidgetJSON && o.systemCartWidgetJSON.name == o.selectedWidget()) {
            return o._getSystemWidget('Cart');
        }
        if (o.widgetSelection() == 'Checkout' && o.systemCheckoutWidgetJSON && o.systemCheckoutWidgetJSON.name == o.selectedWidget()) {
            return o._getSystemWidget('Checkout');
        }
		var _w;
		for (var i in o.jsonSource) {
			if ( i == o.selectedWidget()) {
				_w = new PayLaterWidget(i, o.jsonSource[i]);
				break;
			}
		}
		return _w;
	},
	
	_setProductWidget: function(w) {
		var o = this;
		o.currentProductWidget(w);
		return w.fields();
	},
	_setCartWidget: function(w) {
		var o = this;
		o.currentCartWidget(w);
		return w.fields();
	},
	_setCheckoutWidget: function(w) {
		var o = this;
		o.currentCheckoutWidget(w);
		return w.fields();
	}
}