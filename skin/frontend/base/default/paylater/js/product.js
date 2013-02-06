Product.OptionsPrice.prototype.formatPrice = function (price) {
	if (typeof PayLater != 'undefined') {
		if (PayLater.isTestMode) {
			offer = PayLater.getOffer(price, offer.merchant, 'test');
		} else {
			offer = PayLater.getOffer(price, offer.merchant);
		}
		var representativePop = PayLater.GetFullInfo(offer, representativeLegal);
		if ($('representative-pop')) {
			$('representative-pop').update(representativePop);
			document.getElementById('PayLater-greyBackground').onclick = function () {
				     document.getElementById('PayLater-fullInfo').style.display = "none";
				     document.getElementById('PayLater-greyBackground').style.display = "none";
			 };
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
		return formatCurrency(price, this.priceFormat);
	}
}
