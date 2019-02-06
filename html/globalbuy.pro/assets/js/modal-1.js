function buy(){
	$('.zMessage').html('');
	$('.zEmail').html('');
	$('.zCountType').html('');
	if($(".zBackground").is(':hidden')){
		$(".zBackground").fadeIn('slow');
	}
}
function zClose(){
	if($(".zBackground").is(':visible')){
		$(".zBackground").fadeOut('slow');
	}
}
function validateEmail(email) {
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test(email);
}
function showError(text){
	$('.zMessage').addClass('zError');
	$('.zMessage').html(text);
}
function buyNext(){
	var email = $('.zEmail').val();
	var countAccs = $('input[name=zMaxCount]').val() || 0;
	var selectType = $('input[name=zItem]').val();
	var countType = $('input[name=zCountType]').val();
	var zMinCount = $('input[name=zMinCount]').val();
	coup = $('#coup').val();
	const wallet = $('#funds').val();
	const via = $('#via').val();
	const comment = $('input[name=comment]').val();
	if (!validateEmail(email)) {
		showError('Указан неверный E-mail!');
		return false;
	}
	if (parseInt(countType) <= parseInt(countAccs)) {
	
		if (parseInt(countType) < parseInt(zMinCount)) {
			showError('Минимальное кол-во для заказа: '+zMinCount);
			return false;
		}
		$.post("/order/", {
			email: email,
			count: countType,
			item: selectType,
			wallet: wallet,
			via: via,
			comment: comment,
			code: coup
		}, function (data) {
			try {
				var res = JSON.parse(data);
				console.log('res: ', res)
				if (res.error === false) {
					if (res.redirect == 'yes') {
						location = res.url;
						document.location.href = res.url;
						location.replace(res.url);
						window.location.reload(res.url);
						document.location.replace(res.url);
					} else {
						$('.zPayCount').text(res.count);
						$('.zPayPrice').text(res.price);
						$('.zWallet').html(res.wallet);
						$('.zPayBill').html(res.bill);
						$('.zCheck').attr('onclick', "zCheck('" + res.order + "')");
						$(".zFirst").fadeOut('fast');
						$('.zMessage').html('');
						window.location = '#paymodal';
						$(".zSecond").fadeIn('fast');
					}
				}
			} catch(error) {
				showError('Что-то пошло не так, попробуйте еще раз');
			}
		});	
	}else{
		showError('Такого количества товара нет!');
		return false;
	}
}
function zCheck(url) {
	$('.zMessage').html('');
	$('.zCheck').html('Идёт проверка, ожидайте..');
	$('.zCheck').attr("disabled", "disabled");
	$.get(url, function (data) {
		$('.zCheck').removeAttr("disabled");
		var res = JSON.parse(data);
		if (res.status == "wait_30_sec") {
			showError(res.message);
			$('.zCheck').html('Проверить');
			throw "stop";
		}
		if (res.status == "reset_pass") {
			showError('Владельцу магазина необходимо обновить пароль своего QIWI кошелька!');
			$('.zCheck').html('Проверить');
			throw "stop";
		}
		if (res.status == "Bad_log_pass" || res.status == "error_log_pass") {
			showError('Владельцу магазина необходимо проверить правильность ввода данных QIWI кошелька!');
			$('.zCheck').html('Проверить');
			throw "stop";
		}
		if(res.type=='error'){
			showError(res.alert);
			$('.zCheck').html('Проверить');
		}
		if(res.type=='success'){
			$('.zCheck').attr('onclick', 'window.location ="' + res.order + '"');
			$('.zCheck').html('Скачать');			
		}
	});
}