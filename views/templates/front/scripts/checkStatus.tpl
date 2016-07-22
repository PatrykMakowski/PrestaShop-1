{literal}<script type="text/javascript" language="JavaScript">
/**
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Dotpay Team <tech@dotpay.pl>
 *  @copyright Dotpay
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 */

$(document).ready(function(){
    var timeout = 2;//in minutes
    var interval = 5;//in seconds
    var counter = 0;
    var counterLimit = timeout*60/interval;
    var lastRequest = false;
    setInfoMessage(window.checkStatusConfig.waitingMessage);
    addLoader();
    var checkInt = setInterval(function(){
        if(counter<counterLimit)
            ++counter;
        else {
            clearInterval(checkInt);
            hideLoader();
            setErrorMessage(window.checkStatusConfig.timeoutMessage);
        }
        if(counter==counterLimit-1)
            lastRequest = true;
        $.get(window.checkStatusConfig.url, {"lastRequest":lastRequest}, function(e){
            switch(e) {
                case '0':
                    break;
                case '1':
                    hideLoader();
                    setSuccessMessage(window.checkStatusConfig.successMessage);
                    clearInterval(checkInt);
                    setTimeout(function(){location.href=window.checkStatusConfig.redirectUrl;}, 5000);
                    break;
                default:
                    hideLoader();
                    setErrorMessage(window.checkStatusConfig.errorMessage);
                    clearInterval(checkInt);
            }
            if(e == 'NO' || e == '-1') {
                hideLoader();
                setErrorMessage(window.checkStatusConfig.errorMessage);
            }
        });
    }, interval*1000);
});

function setMessage(message, className) {
    $('#statusMessageContainer p').remove();
    var element = document.createElement('p');
    element.className = 'alert '+className;
    element.innerHTML = message;
    $('#statusMessageContainer').append(element);
}

function setErrorMessage(message) {
    setMessage(message, 'alert-danger');
}

function setSuccessMessage(message) {
    setMessage(message, 'alert-success');
}

function setInfoMessage(message) {
    setMessage(message, 'alert-info');
}

function addLoader() {
    var element = document.createElement('div');
    element.className = 'loading';
    $('#statusMessageContainer').append(element);
}

function hideLoader() {
    $('#statusMessageContainer .loading').remove();
}

</script>{/literal}