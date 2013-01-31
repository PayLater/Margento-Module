Validation.add('change-to-uppercase', '', function(v) {
	if(v.length){
		$('paylater_merchant_guid').value = v.toUpperCase();
		return true;
	}
	return true;
});