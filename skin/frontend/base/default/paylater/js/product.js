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

