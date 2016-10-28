var tender = {
    addToDB: function () {
        let tenderName = document.getElementById('tenderName').value,
            tenderPrice = document.getElementById('tenderPrice').value,
            body = "action=add&name=" + tenderName + '&price=' + tenderPrice;
        xhrSend("process.php", body);
    },
    getFromDB: function () {
        let body = "action=get";
        xhrSend("process.php", body);
    },
    truncateDB: function () {
        let body = 'action=truncate';
        xhrSend('process.php', body);
    },
    removeFromDB: function (num) {
        let body = 'action=remove&id=' + num;
        xhrSend('process.php', body);
    },
    loginCheck: function () {
        let login = document.getElementById('login-field').value,
            password = document.getElementById('password-field').value,
            body = "action=check&login=" + login + "&password=" + password;

        xhrSend("login.php", body);
    },
    logOut: function(){
        let body = 'action=logout';
        xhrSend('login.php', body);
    },
    callbackMapper: {
        dataSelected: function (response) {
            let tenders=response['data'],
            tendersCount = tenders.length;

            emptyTendersList();
            for (var i = 0; i < tendersCount; i++) {
                pushNewTenderDiv(tenders[i]);
            }
        },
        recordDeleted: function () {
            console.log('table changed, refreshing now..');
            tender.getFromDB();
        },
        tableTruncated: function () {
            console.log('table changed, refreshing now..');
            tender.getFromDB();
        },
        recordAdded: function () {
            console.log('table changed, refreshing now..');
            tender.getFromDB();
        },
        loginChecked: function (response) {
            document.getElementById('auth-form').innerHTML = "<div>Здравствуйте, " +
                response.data[0].login + "</div>" +
				"<button class='tender-submit-button' onclick='tender.logOut()'>LOGOUT</button>";
        },
        logOut: function (response) {
            document.getElementById('auth-form').innerHTML = "<input type='text' placeholder='login' id='login-field'>\
			<input type='password' placeholder='password' id='password-field'>\
			<button class='tender-submit-button' onclick='tender.loginCheck()'>LOGIN</button>";
        }
    }
}

// listening server for refresh command
let eventSource = new EventSource('eventTransmitter.php');
eventSource.onmessage = function (e) {
    console.log(e);
}

document.addEventListener('DOMContentLoaded', tender.getFromDB);

function responseHandle(data) {
    // parsed response looks like {"responseType": "type", "data": [{..}, {..}..]}
    let response = JSON.parse(data),
        actionRequired = response.responseType;

    // skipping all responses except ones which types
    // are known and provided by server php
    if (!actionRequired || !tender.callbackMapper[actionRequired]) {
        return;
    }
    tender.callbackMapper[actionRequired](response);
}

function pushNewTenderDiv(data) {
    let newDiv = document.createElement('div'),
        list = document.getElementById('tender-content'),
        thisId = data.id;
    newDiv.setAttribute('data-id', thisId);
    newDiv.classList.add('tender-div');
    newDiv.addEventListener('click', function () {
        tender.removeFromDB(thisId);
        console.log('removing record number ' + thisId);
    });
    newDiv.innerHTML = '<div class="inside-name">' + thisId + '. ' +
        data.name + '</div><div class="inside-price">' + data.price + ' ₽</div>';
    list.appendChild(newDiv);
}

function emptyTendersList() {
    document.getElementById('tender-content').innerHTML = '';
}

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