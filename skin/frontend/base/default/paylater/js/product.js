Product.OptionsPrice.prototype.formatPrice = function (price) {
	if (typeof PayLater != 'undefined') {
		representative = PayLater.GetPaymentsWidget(price, 'product');
		if ($('representative-holder')) {
			$('representative-holder').update(representative);
		}
	}
	return formatCurrency(price, this.priceFormat);
}
