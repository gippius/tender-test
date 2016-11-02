var tender = {
    // page event handlers
    addToDB: function () {
        let tenderName = document.getElementById('tenderName').value,
            tenderPrice = document.getElementById('tenderPrice').value,
            body = "action=add&name=" + tenderName + '&price=' + tenderPrice;
        xhrSend("process.php", body);
    },
    refreshTendersData: function () {
        let body = "action=get";
        xhrSend("process.php", body);
    },
    sessionCheck: function () {
        let body = "action=sessionCheck";
        xhrSend("login.php", body);
    },
    truncateDB: function () { // deprecated
        let body = 'action=truncate';
        xhrSend('process.php', body);
    },
    markAsCompleted: function (num) {
        let body = 'action=markcompleted&id=' + num;
        xhrSend('process.php', body);
    },
    loginCheck: function () {
        let login = document.getElementById('login-field').value,
            password = document.getElementById('password-field').value,
            body = "action=login&login=" + login + "&password=" + password;
        xhrSend("login.php", body);
    },
    logOut: function () {
        let body = 'action=logout';
        xhrSend('login.php', body);
    },
    sessionCheck: function () {
        let body = 'action=sessioncheck';
        xhrSend('login.php', body);
    },
    // stores callback functions for every type
    // of server 'responseType' answer
    callbackMapper: {
        dataSelected: function (response) {
            console.log(response);
            let tenders = response.data,
                tendersCount = tenders.length;
            emptyTendersList();
            for (var i = 0; i < tendersCount; i++) {
                pushNewTenderDiv(tenders[i]);
            }
        },
        recordAdded: function () {
            console.log('Tender added, refreshing table');
            tender.refreshTendersData();
            tender.sessionCheck();
        },
        tenderCompleted: function (response) {
            console.log(response);
            tender.sessionCheck();
            tender.refreshTendersData();
        },
        loggedIn: function (response) {
            console.log(response);
            refreshControlForms(response);
            tender.refreshTendersData();
        },
        sessionChecked: function (response) {
            console.log(response);
            refreshControlForms(response);
            tender.refreshTendersData();
        },
        error: function (response) {
            console.log(response.message);
        },
        loggedOut: function (response) {
            console.log(response.data);
            document.getElementById('auth-form').innerHTML = "<input type='text' placeholder='login' id='login-field'>\
			<input type='password' placeholder='password' id='password-field'>\
			<button class='tender-submit-button' onclick='tender.loginCheck()'>LOGIN</button>";

            document.getElementById('tender-nav').innerHTML = '';
            tender.refreshTendersData();
        },
        clientCannotWinTenders: function (response) {
            console.log(response[0]);
        }
    }
}

// first load
document.addEventListener('DOMContentLoaded', tender.sessionCheck);
document.addEventListener('DOMContentLoaded', tender.refreshTendersData);

function responseHandle(response) {
    // parsed response looks like 
    // {"responseType": "type", "data/message" (if any): [{..}, {..}..]}
    let parsedResponse = JSON.parse(response),
        responseType = parsedResponse.responseType,
        data = parsedResponse.data;
    // skipping all responses except ones which types
    // are known and described above in 'callbackMapper'
    //
    // DEBUG
    console.log(parsedResponse);
    if (!tender.callbackMapper[responseType]) {
        return;
    }
    tender.callbackMapper[responseType](parsedResponse);
}

function pushNewTenderDiv(data) {
    let newDiv = document.createElement('div'),
        list = document.getElementById('tender-content'),
        thisId = data.id;
    newDiv.setAttribute('data-id', thisId);
    newDiv.classList.add('tender-div');
    newDiv.addEventListener('click', function () {
        tender.markAsCompleted(thisId);
        // console.log('removing record number ' + thisId);
    });
    newDiv.innerHTML = '<div class="inside-name">' + thisId + '. ' +
        data.name + '</div><div class="inside-price">' + data.price + ' ₽</div>';
    list.appendChild(newDiv);
}

function emptyTendersList() {
    document.getElementById('tender-content').innerHTML = '';
}

function refreshControlForms(response) {
    let user = response.data[0];
    document.getElementById('auth-form').innerHTML = "<div id='user-welcome'>" +
        user.name + "<br> Ваш счёт: " + user.balance + "₽<br>Статус: " + user.usertype + "</div>" +
        "<button class='tender-submit-button' onclick='tender.logOut()'>LOGOUT</button>";
    if (user.usertype == 'client') {
        document.getElementById('tender-nav').innerHTML = '<section class="tender-control">\
			<div id="tenderNameContainer">\
				<div><input type="text" name="tenderName" class="tender-input" id="tenderName" placeholder="NAME"></div>\
			</div>\
			<div id="tenderPriceContainer">\
				<div><input type="text" name="tenderPrice" class="tender-input" id="tenderPrice" placeholder="PRICE"></div>\
			</div>\
			<input type="hidden" name="tenderHash">\
			<div>\
				<button onclick="tender.addToDB()" class="tender-submit-button">SUBMIT</button>\
			</div>\
		</section>';
    }
}

// shorthand for AJAX, native way
function xhrSend(url, body) {
    let r = new XMLHttpRequest();
    r.open("POST", url, true);
    r.addEventListener("load", function () {
        if (r.readyState == 4 && r.status == 200) { 
            responseHandle(r.responseText);
        }
    });
    r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    r.send(body);
}