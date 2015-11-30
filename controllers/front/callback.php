<?php
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

class dotpaycallbackModuleFrontController extends ModuleFrontController
{
    public function displayAjax()
    {
        
	if($_SERVER['REMOTE_ADDR'] == '77.79.195.34' and $_SERVER['REQUEST_METHOD'] == 'GET')
            die("PrestaShop - M.Ver: ".$this->module->version.", P.Ver: ". _PS_VERSION_ .", ID: ".Configuration::get('DP_ID').", Active: ".Configuration::get('DOTPAY_CONFIGURATION_OK').", Test: ".Configuration::get('DP_TEST').", CHK: ".Configuration::get('DP_CHK').", HTTPS: ".Configuration::get('DP_SSL').", P.SSL: ".Configuration::get('PS_SSL_ENABLED'));        
        
		if($_SERVER['REMOTE_ADDR'] <> '195.150.9.37')
			die("PrestaShop - ERROR (REMOTE ADDRESS: ".$_SERVER['REMOTE_ADDR'].")"); 	  
		
		if ($_SERVER['REQUEST_METHOD'] <> 'POST')
		    die("PrestaShop - ERROR (METHOD <> POST)");  	
			

        if(!Dotpay::check_urlc_legacy())
            die("PrestaShop - LEGACY MD5 ERROR - CHECK PIN");

			$cart = new Cart((int)Tools::getValue('control'));
			$customer = new Customer((int)$cart->id_customer);
			$orginal_amount = trim(Tools::getValue('orginal_amount'));
			$currency = Currency::getCurrency((int)$cart->id_currency);
			$currency_self = Currency::getCurrency((int)self::$cart->id_currency);
			$total = (float)$cart->getOrderTotal();
			
			if(version_compare(_PS_VERSION_, "1.6.1", ">=")) {
				$D_amount = explode(" ",trim(Tools::getValue('amount')));
					if($currency["iso_code"] <> $currency_self["iso_code"] && $orginal_amount <> $D_amount[0] ){  //if you have used 2 Currency
						$price = number_format(($total*$currency["conversion_rate"]), 2,'.', ''); 
						$price .= " ".$currency["iso_code"];
					}else{					
						$price = number_format($total, 2,'.', ''); 
						$price .= " ".$currency["iso_code"];
					}	
				
			} else{
					$price = number_format($total, 2,'.', ''); 
					$price .= " ".$currency["iso_code"];
			}
			
		if($currency["iso_code"] <> $currency_self["iso_code"]){
			if(round($price,1) <> round($orginal_amount,1)) //jesli waluta gl. rozna od waluty zamownienia, fix dla roznic z przewalutowania
				die('PrestaShop - NO MATCH OR WRONG AMOUNT - '.$price.' <> '.$orginal_amount);
		}else{
			if($price <> $orginal_amount)
				die('PrestaShop - NO MATCH OR WRONG AMOUNT - '.$price.' <> '.$orginal_amount);
		}


        switch (Tools::getValue('t_status'))
        {
            case 1:
                $actual_state = Configuration::get('PAYMENT_DOTPAY_NEW_STATUS');
                break;
            case 2:
                $actual_state = _PS_OS_PAYMENT_;
                break;
            case 3:
                $actual_state = _PS_OS_ERROR_;
                break;
            case 4:
                $actual_state = _PS_OS_ERROR_;
                break;
            case 5:
                $actual_state = Configuration::get('PAYMENT_DOTPAY_COMPLAINT_STATUS');   
            default:
                die ("PrestaShop - WRONG TRANSACTION STATUS");
        }
		
        if($cart->OrderExists() == false)
        {
            $this->module->validateOrder($cart->id, $actual_state, $price, $this->module->displayName, NULL, array(), (int)$cart->id_currency, false, $customer->secure_key);
            echo "OK";
        }
        else
        {
            $history = new OrderHistory();
            $history->id_order = Order::getOrderByCartId((int)Tools::getValue('control'));
            $lastOrderState = OrderHistory::getLastOrderState($history->id_order);
            if($lastOrderState->id <> $actual_state)
            {
                $history->changeIdOrderState($actual_state, $history->id_order);
                $history->addWithemail(true);
            }
            echo "OK";
        }
    }
}
