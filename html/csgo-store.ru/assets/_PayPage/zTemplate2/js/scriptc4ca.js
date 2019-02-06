// Функция проверки почты регуляркой
function validateEmail(email) {
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test(email);
}
var i = 0;
// отправляем заказ
function buyNext(offer = false){
	
	var email = $('input[secret=email]').val();

	if (offer == false && !validateEmail(email)) {
		toastr.error('Указан неверный E-mail!');
		return false;
	}
	var countType = $('input[secret=count]').val() || 0;
	
	if (countType == false || countType == 0) {
		toastr.error('Количество товара для покупки не может быть равно нулю!');
		return false;
	}

	$('.panel-footer').hide(500);
	$('#twoStep').hide(500);
	$('.xpreloader').show();


	var countAccs = $('input[secret=count]').attr('max');
	var selectType = $('input[secret=id]').val();
	var zMinCount = $('input[secret=count]').attr('min');
	var coup = $('input[secret=code]').val();
	var wallet = $('.selected').attr('method');
	var via = $('.selected').attr('via');
	var comment = $('input[secret=comment]').val();


	if (parseInt(countType) <= parseInt(countAccs) || offer == true) {
	
		if (parseInt(countType) < parseInt(zMinCount) && offer !== true) {
			toastr.error('Минимальное кол-во для заказа: '+zMinCount);
			$('#twoStep').show(500);
			$('.xpreloader').hide();
			return false;
		}
		$.post("/order/"+(offer ? $('input[secret=bill]').val()+'/getOffer' : ''), {
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
				if (res.error === false) {
					if (res.redirect == 'yes') {
						document.location.href = res.url;
					} else if (res.formType == 'old') {
						$('.xpreloader').hide(200);
						$('#payPage').show(200);
						$('input[secret=order_link]').val(res.order_link);
						$('input[secret=payCount]').val(countType);
						$('input[secret=payPrice]').val(res.price);
						$('input[secret=payWallet]').val(res.wallet);
						$('input[secret=payDesc]').val(res.bill);
					} else {
						$('span[secret=payDesc]').html(res.bill);
					}
				} else {
					try {
						toastr.error(res.error);
						setTimeout(function(){
							location.replace('/');
						}, 1000);
					} catch(error){
						toastr.error(res.alert);
					}
					
				}

			} catch(error) {
				toastr.error('Что-то пошло не так, попробуйте еще раз');
			}
		});	
	}else{
		toastr.error('Такого количества товара нет!');
		$('#twoStep').show(500);
		$('.xpreloader').hide();
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
			toastr.error(res.message);
			$('.zCheck').html('Проверить');
			throw "stop";
		}
		if (res.status == "reset_pass") {
			toastr.error('Владельцу магазина необходимо обновить пароль своего QIWI кошелька!');
			$('.zCheck').html('Проверить');
			throw "stop";
		}
		if (res.status == "Bad_log_pass" || res.status == "error_log_pass") {
			toastr.error('Владельцу магазина необходимо проверить правильность ввода данных QIWI кошелька!');
			$('.zCheck').html('Проверить');
			throw "stop";
		}
		if(res.type=='error'){
			toastr.error(res.alert);
			$('.zCheck').html('Проверить');
		}
		if(res.type=='success'){
			$('.zCheck').attr('onclick', 'window.location ="' + res.order + '"');
			$('.zCheck').html('Скачать');			
		}
	});
}

// setTimeout(function () {
// 	$('.staticModal .wrapper .overlay').fadeToggle(200);
// 	toastr.info('Первый этап<br>Выбор платежной системы');
// }, 700);

/*Вернуться к выбору оплаты*/
$(document).on('click', '#returnToPick', function() {
	// $('.wrapper').width('950')
	$('#returnToPick').removeClass('selected');
	$('#twoStep').hide(500);
	$('#oneStep').show();
	setTimeout(function () {
		$('.staticModal .wrapper .overlay').fadeToggle(200);
		toastr.info('Первый этап<br>Выбор платежной системы');
	}, 700);

});

/* "калькулятор стоимости" ( умножаем цену на количество.. )*/
function calc(){

	if(parseInt($('input[secret=count]').val()) < 0) $('input[secret=count]').val(1);

	if(parseInt($('input[secret=count]').val()) >= parseInt($('input[secret=count]').attr('max'))) $('input[secret=count]').val($('input[secret=count]').attr('max'));

	var price = parseInt($('input[secret=wmr]').val()) * parseInt($('input[secret=count]').val());

	$('span[secret=price]').text(price);

};

$(document).on('click', '.copy', function() {
	toastr.success('Скопировано в буфер обмена.');
});

$(document).on('click', '.back', function() {
	location.reload();
	return false;
});

/* Оплатить товар*/
$(document).on('click', '.RulesAccept', function() {
	$('#completePayment').toggleClass('disabled');
	$('.rules').toggleClass('hidden');
	$('#completePaymentsButton').toggleClass('hidden');
});

$(document).on('click', '#completePayment', function() {

	if($('#completePayment').hasClass('disabled')) return toastr.error('Покупка невозможна без соглашения с правилами магазина!');

	if($('input[secret=email]').val() != '' && $('input[secret=count]').val() != ''){

		buyNext();

	} else toastr.warning('Заполните обязательные поля!');
});/* Оплатить заказ*/
$(document).on('click', '#payOffer', function() {
	if($('input[secret=email]').val() != '' && $('input[secret=count]').val() != ''){

		buyNext(true);

	} else toastr.warning('Заполните обязательные поля!');
});

/* Оплатить товар*/
$(document).on('click', '#waitOrder', function() {
	$('#payPage').hide(500);
	$('.xpreloader').show();
	$('#waitorder').show();

	if($('input[secret=order_link]').val() != ''){
        if(i==0){
            startTimer()
            var timer=setInterval(function(){
            	check_pay($('input[secret=order_link]').val());
            }, 6000);
        }
        function ch(i){
            clearTimeout(timer);
        }
    }else{
    	alert('error21312');
    }
});


$(document).on('click', '.methods .method', function() {
	$('#oneStep').hide();
	$('#twoStep').show();
});


$(document).on('click', '.methods .method', function() {

	$('.methods .method').removeClass('selected');
	$(this).addClass('selected')
	if($('.selected').length == 1) {
		if($('.selected').attr('method') != undefined) {
			$('#completePayment').text('Оплатить ' + $('.selected').attr('m_name'));
			$('.staticModal .wrapper .overlay').fadeToggle(200);
			// $('.wrapper').width('550')
			$('#oneStep').hide(500);
			$('#twoStep').show();
			setTimeout(function () {
				$('.staticModal .wrapper .overlay').fadeToggle(200);
				toastr.info('Второй этап<br>Заполнение полей');
			}, 700);
		} else {
			toastr.error('Произошла какая-то ошибка, обновите страницу!');
		}
	} else {
	toastr.error('Пожалуйста, выберите способ оплаты!');
	}
});
        function startTimer(){
            waitTimern = 6;
            setInterval(function(){
                $("#waitTimer").html(--waitTimern);
                if(waitTimern < 1){
                    waitTimern = 6;
                } else {
                    //waitTimern = 4;
                }
            }, 1000);
        }

        function check_pay(order){
            $.post("/order/" + order, {}, function(data){
                var res = JSON.parse(data);         
                if(res.error == false){
                    //console.log(res);
                    location.replace('/getorder/' + order);
                    i++;
                    ch(i);
                    //location.replace('/');
                } else if(res.error != ""){
                    if(res.error == true){
                    	if(res.alert == "\u041f\u043b\u0430\u0442\u0435\u0436 \u043d\u0435 \u043d\u0430\u0439\u0434\u0435\u043d :(  \u041f\u043e\u043f\u0440\u043e\u0431\u0443\u0439\u0442\u0435 \u0435\u0449\u0435 \u0440\u0430\u0437 \u0447\u0435\u0440\u0435\u0437 \u043c\u0433\u043d\u043e\u0432\u0435\u043d\u0438\u0435 :)"){
                       		$("#cresult").html('Ошибка, платеж не найден. Возможно он еще обрабатывается.');
                    	}else{
							$("#cresult").html(res.alert);
                    	}
                    }else if(res.alert == "\u0422\u0430\u043a\u043e\u0433\u043e \u0437\u0430\u043a\u0430\u0437\u0430 \u043d\u0435\u0442."){
                        alert("Такого товара нет в системе.");
                        location.replace('/');
                    }
                }
            });
        }

// $(document).on('click', '#nextStep', function() {

// 	if($('.selected').length == 1) {
// 		if($('.selected').attr('method') != undefined) {
// 			$('.staticModal .wrapper .overlay').fadeToggle(200);
// 			$('#oneStep').hide(500);
// 			$('#twoStep').show();
// 			setTimeout(function () {
// 				$('.staticModal .wrapper .overlay').fadeToggle(200);
// 				toastr.warning('Второй этап');
// 			}, 700);
// 		} else {
// 			toastr.error('Произошла какая-то ошибка, обновите страницу!');
// 		}
// 	} else {
// 		toastr.error('Пожалуйста, выберите способ оплаты!');
// 	}

// });
