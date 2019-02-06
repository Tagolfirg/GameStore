var getedId=0;
var numOfItems=0;
var selectedValueId=0;
var setedWayForMoney=0;
var firstInstrStat=false;
var secondInstrStat=false;
var thirdInstrStat=false;
var getedInstrId=0;
var instrIdStat=[];
var getedInstNewId=0;
var agreeLicenseChecked=false;

function BuyButtonClick(getedId){

	numOfItems=document.getElementById('number-of-items-'+getedId).value;
	document.getElementById('end-number').value=numOfItems;
	document.getElementById('item-selected').value=getedId;
	//document.getElementById('item-id').value=getedId;
}

function setWayForMoney(setedWayForMoney){
	document.getElementById('walletsSelect').value=setedWayForMoney;
}

function setEmail(){
	document.getElementById('row-box-email').value=document.getElementById('alert-box-email').value;
	sendData();
}

function instrClick(getedInstId){
	if ((instrIdStat[getedInstId] == false) || (instrIdStat[getedInstId] == null)) {
		instClose(1);
		instClose(2);
		instClose(3);
		instClose(4);
		instOpen(getedInstId);
	} else {
		instClose(getedInstId);
	}
}

function instOpen(getedInstNewId){
	document.getElementById('instrArrow'+getedInstNewId).src="\arrowDown.png";
	document.getElementById('instrText'+getedInstNewId).style.display="block";
	instrIdStat[getedInstNewId] = true;
}

function instClose(getedInstNewId){
	document.getElementById('instrText'+getedInstNewId).style.display="none";
	document.getElementById('instrArrow'+getedInstNewId).src="\arrow.png";
	instrIdStat[getedInstNewId] = false;	
}

function checkAgreeLicense(){
	if (document.getElementById('agreeLicense').checked==true){
		document.getElementById('setEmailButton').disabled=false;
	} else {
		document.getElementById('setEmailButton').disabled=true;
	}
}

function checkEmail(){
	emailInput=document.getElementById('alert-box-email');
	var emailString = emailInput.value;
	var correctEmailString="";
	var theUserIsMSchoolar=false;
	for (var i=0;i<emailString.length;i++){
		if (checkSymbolOfEmail(emailString.charAt(i))==true) {
			correctEmailString=correctEmailString+emailString.charAt(i);
		} else {
			theUserIsMSchoolar=true;
		}
	}
	if (theUserIsMSchoolar==true){
		emailInput.value=correctEmailString;
	}
}



function checkSymbolOfEmail(checkingSymbol){
	if (checkingSymbol >= "a" && checkingSymbol <= "z") {
		return true;
	}
	if (checkingSymbol >= "A" && checkingSymbol <= "Z") {
		return true;
	}
	if (checkingSymbol >= "0" && checkingSymbol <= "9") {
		return true;
	}
	switch (checkingSymbol){
		case "@":return true;break;
		case ".":return true;break;
		case "!":return true;break;
		case "#":return true;break;
		case "$":return true;break;
		case "%":return true;break;
		case "&":return true;break;
		case "'":return true;break;
		case "*":return true;break;
		case "+":return true;break;
		case "-":return true;break;
		case "=":return true;break;
		case "/":return true;break;
		case "?":return true;break;
		case "^":return true;break;
		case "_":return true;break;
		case "`":return true;break;
		case "{":return true;break;
		case "}":return true;break;
		case "|":return true;break;
		case "~":return true;break;
	}
}