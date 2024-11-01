function postAjax(data, successCallback) {
    let params = typeof data == 'string' ? data : Object.keys(data).map(
        function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
    ).join('&');

    let xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

    xhr.open('POST', ajaxurl);

    xhr.onreadystatechange = function() {
        if (xhr.readyState > 3 && xhr.status == 200) {
            let json = JSON.parse(xhr.responseText)
            if (json)
                successCallback(json);
        }
    };

    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);

    return xhr;
}

function emptyTestConnectionResultMessageElement() {
    let resultElement = document.getElementById('wc-nettix-integration-test-connection-result');
    resultElement.childNodes.forEach(function(e) { e.remove() });
}

function testConnectionSuccessCallBack(data) {
    document.getElementById("wc-nettix-integration-test-connection-spinner").setAttribute("class", "spinner");

    let resultMessage = document.createTextNode(data.message);
    let resultElement = document.getElementById('wc-nettix-integration-test-connection-result');

    emptyTestConnectionResultMessageElement();
    resultElement.appendChild(resultMessage);
}

function testConnection() {
    emptyTestConnectionResultMessageElement();
    document.getElementById("wc-nettix-integration-test-connection-spinner").setAttribute("class", "spinner is-active");
    let clientId = document.getElementById('woocommerce_wc-integration-to-nettix_nettix_client_id').value;
    let clientSecret = document.getElementById('woocommerce_wc-integration-to-nettix_nettix_secret_id').value;
    let userIds = document.getElementById('woocommerce_wc-integration-to-nettix_nettix_user_ids').value;
    let car = (document.getElementById('woocommerce_wc-integration-to-nettix_nettix_service_car').checked) ? 1 : 0;
    let bike = (document.getElementById('woocommerce_wc-integration-to-nettix_nettix_service_bike').checked) ? 1 : 0;
    let boat = (document.getElementById('woocommerce_wc-integration-to-nettix_nettix_service_boat').checked) ? 1 : 0;

    const url = 'https://auth.nettix.fi/oauth2/token';

    postAjax({action: 'wc_integration_to_nettix_test_connection', client_id: clientId, client_secret: clientSecret, user_ids: userIds, service_car: car, service_bike: bike, service_boat: boat}, testConnectionSuccessCallBack);
}