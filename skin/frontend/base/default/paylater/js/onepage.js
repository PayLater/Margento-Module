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