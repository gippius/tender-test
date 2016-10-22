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
    removeRecord: function (num) {
        let body = 'action=remove&id=' + num;
        xhrSend('process.php', body);
    }
}


let eventSource = new EventSource('eventTransmitter.php');
eventSounce.onmessage = function(e){
console.log(e);
}

function responseHandle(data) {



    let actionRequired = 'addOnPage',
        response = JSON.parse(data),
        tendersCount = response.length,
        callbackMapper = {
            addOnPage: function (response) {
                emptyTendersList();
                for (var i = 0; i < tendersCount; i++) {
                    console.log(response[i]);
                    pushNewTenderDiv(response[i]);
                }

            }
        }
    callbackMapper[actionRequired](response);
}

function pushNewTenderDiv(data) {
    let newDiv = document.createElement('div'),
        list = document.getElementById('tender-content');

    newDiv.innerHTML = '<b>' + data.id + '</b><br>' +
        data.name + '<br>' + data.price + 'rur';

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
