function price_rub() {
	$('.dlrprice').each(function() {
		var price = $(this);
		price.hide();
	});
	$('.rubprice').each(function() {
		var price = $(this);
		price.show();
	});
};

function price_dlr() {
	$('.rubprice').each(function() {
		var price = $(this);
		price.hide();
	});
	$('.dlrprice').each(function() {
		var price = $(this);
		price.show();
	});
};

	function validateEmail(email){ 
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

function showerr(data)
{
	$().toastmessage('showToast', {
		text     : data,
		sticky   : false,
		position : 'top-right',
		type     : 'warning'
	});
}

function showmsg(data)
{
	$().toastmessage('showToast', {
		text     : data,
		sticky   : false,
		position : 'top-right',
		type     : 'notice'
	});
}

function sendData(){
    var email = $('input[name=email]').val();
	var count = $('input[name=count]').val() || 0;
	var item = $('select[name=item]').val();
	var comment = $('input[name=comment]').val();
	var minCount = $('option[value="' + item + '"]').attr('data-min_order');
	var countItem = $('td[data-id=' + item + ']').html();

	if (!validateEmail(email)){
		var err = 'Указан неверный email адрес';
		showerr(err);
		return false;
	}
	
	if (parseInt(count) < parseInt(minCount)){
		var err = 'Мин. кол-во для заказа: ' + minCount;
		showerr(err);
		return false;
	}
	
	if (parseInt(countItem) < parseInt(count)){
		var err = 'Такого количества товара нет';
		showerr(err);
		return false;
	}
	if (typeof(via) == 'undefined') {
		via = '';
	}
	$.post("/order/", {email: email, count: count, via: via, item: item, code: $("#cupon").val(), wallet: $('select[name=wallets]').val()}, function(data){
		try {
			var res = JSON.parse(data);			
			if(res.error == false){
				$("#selectPay").hide();
				$("#paymodal").show();
				$('.paytable .payitem').html(res.item);
				$('.paytable .paycount').html(res.count);
				$('.paytable .payprice').html(res.price);
				$('.paytable .paywallet').html(res.wallet);
				$('.paytable .paybill').html(res.bill);
				$('.checkpaybtn').attr('onclick',"checkpay('" + res.order + "');");
				$('#paymodal').modal('toggle');
			} else if(res.error != ""){
				showerr(res.error);
			}
		} catch(err){}
	}); 
}

function checkpay(url){
	$('.checkpaybtn').attr('disabled','');
	$(".checkpaybtn").val("Идёт проверка..");	
	$.get(url, function(data){
		if(typeof(data)!="undefined"){
			$(".checkpaybtn").removeAttr("disabled");	
			$(".checkpaybtn").val("Проверить");	
		}
		var res = JSON.parse(data);
		if(res.error == false){
			$('.checkpaybtn').attr('onclick','window.location ="'+res.order+'"');
			$(".checkpaybtn").val('Скачать файл');
		} else {
			alert('Платеж не найден! Попробуйте позже');
		}
	});
}