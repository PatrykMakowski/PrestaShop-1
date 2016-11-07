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

require_once(mydirname(__DIR__,3).'/models/Config.php');

/**
 * Abstract controller for other Dotpay plugin controllers
 */
abstract class DotpayController extends ModuleFrontController {
    /**
     *
     * @var DotpayConfig Dotpay configuration 
     */
    protected $config;
    
    /**
     *
     * @var Customer Object with customer data
     */
    protected $customer;
    
    /**
     *
     * @var Address Object with customer address data
     */
    protected $address;
    
    /**
     *
     * @var DotpayApi Api for selected Dotpay payment API (dev or legacy)
     */
    protected $api;
    
    /**
     * Prepares environment for all Dotpay controllers
     */
    public function __construct() {
        parent::__construct();
        $this->config = new DotpayConfig();
        
        $this->initPersonalData();
        
        if($this->config->getDotpayApiVersion()=='legacy') {
            $this->api = new DotpayLegacyApi($this);
        } else {
            $this->api = new DotpayDevApi($this);
        }
        
        $this->module->registerFormHelper();
    }
    
    /**
     * Returns seller ID
     * @return string
     */
    public function getDotId() {
        return $this->config->getDotpayId();
    }
    
    /**
     * Returns last order number
     * @return string
     */
    public function getLastOrderNumber() {
        return Order::getOrderByCartId($this->context->cart->id);
    }


    /**
     * Returns unique value for every order
     * @return string
     */
    public function getDotControl($source = NULL) {
        if($source == NULL)
            return $this->getLastOrderNumber().'|'.$_SERVER['SERVER_NAME'];
        else {
            $tmp = explode('|', $source);
            return $tmp[0];
        }
    }
    
    /**
     * Returns title of shop
     * @return string
     */
    public function getDotPinfo() {
        return Configuration::get('PS_SHOP_NAME');
    }
    
    /**
     * Returns amount of order
     * @return float
     */
    public function getDotAmount() {
        return $this->api->getFormatAmount(
            Tools::displayPrice(
                $this->context->cart->getOrderTotal(true, Cart::BOTH), new Currency($this->context->cart->id_currency)
            )
        );
    }
    
    /**
     * Returns amount of shipping
     * @return float
     */
    public function getDotShippingAmount() {
        return $this->api->getFormatAmount(
            Tools::displayPrice(
                $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING), new Currency($this->context->cart->id_currency)
            )
        );
    }
    
    /**
     * Returns code of currency used in order
     * @return string
     */
    public function getDotCurrency() {
        $currency = Currency::getCurrency($this->context->cart->id_currency);
        return $currency["iso_code"];
    }
    
    /**
     * Returns id of order currency
     * @return int
     */
    public function getDotCurrencyId() {
        $currency = Currency::getCurrency($this->context->cart->id_currency);
        return $currency["id_currency"];
    }
    
    /**
     * Returns description of order
     * @return string
     */
    public function getDotDescription() {
        $order = new Order(Order::getOrderByCartId($this->context->cart->id));
        if($this->config->getDotpayApiVersion() == 'dev')
            return ($this->module->l("Order ID:").' '.$order->reference);
        else
            return ($this->module->l("Your order ID:").' '.$order->reference);
    }
    
    /**
     * Returns language code for customer language
     * @return string
     */
    public function getDotLang() {
        $lang = strtolower(LanguageCore::getIsoById($this->context->cookie->id_lang));
        if(in_array($lang, $this->config->getDotpayAvailableLanguage())) {
            return $lang;
        } else {
            return "en";
        }
    }
    
    /**
     * Returns name of server protocol, using by shop
     * @return string
     */
    public function getServerProtocol() {
        $result = 'http';
        
        if($this->module->isSSLEnabled()) {
            $result = 'https';
        }
        
        return $result;
    }
    
    /**
     * Returns URL of site where Dotpay redirect after payment
     * @return string
     */
    public function getDotUrl() {
        return $this->context->link->getModuleLink('dotpay', 'back', array('control' => $this->context->cart->id), $this->module->isSSLEnabled());
    }
    
    /**
     * Returns URL of site where Dotpay send URLC confirmations
     * @return string
     */
    public function getDotUrlC() {
        return $this->context->link->getModuleLink('dotpay', 'callback', array('ajax' => '1'), $this->module->isSSLEnabled());
    }
    
    /**
     * Returns firstname of customer
     * @return string
     */
    public function getDotFirstname() {
        return $this->customer->firstname;
    }
    
    /**
     * Returns lastname of customer
     * @return string
     */
    public function getDotLastname() {
        return $this->customer->lastname;
    }
    
    /**
     * Returns email of customer
     * @return string
     */
    public function getDotEmail() {
        return $this->customer->email;
    }
    
    /**
     * Returns phone of customer
     * @return string
     */
    public function getDotPhone() {
        $phone = '';
        if($this->address->phone != '')
            $phone = $this->address->phone;
        else if($this->address->phone_mobile != '')
            $phone = $this->address->phone_mobile;
        return $phone;
    }
    
    /**
     * Returns street and building number even if customer didn't get a value of building number
     * @return array
     */
    public function getDotStreetAndStreetN1() {
        $street = $this->address->address1;
        $street_n1 = $this->address->address2;
        
        if(empty($street_n1))
        {
            preg_match("/\s[\w\d\/_\-]{0,30}$/", $street, $matches);
            if(count($matches)>0)
            {
                $street_n1 = trim($matches[0]);
                $street = str_replace($matches[0], '', $street);
            }
        }
        
        return array(
            'street' => $street,
            'street_n1' => $street_n1
        );
    }
    
    /**
     * Returns a city of customer
     * @return string 
     */
    public function getDotCity() {
        return $this->address->city;
    }
    
    /**
     * Returns a postcode of customer
     * @return string 
     */
    public function getDotPostcode() {
        return $this->address->postcode;
    }
    
    /**
     * Checks if PV card channel for separated currencies is enabled
     * @return boolean
     */
    public function isDotpayPVEnabled() {
        $result = $this->config->isDotpayPV();
        if(!$this->isDotSelectedCurrency($this->config->getDotpayPvCurrencies())) {
            $result = false;
        }
        return $result;
    }
    
    /**
     * Checks if main channel is enabled
     * @return boolean
     */
    public function isMainChannelEnabled() {
        if($this->isDotSelectedCurrency($this->config->getDotpayWidgetDisCurr())) {
            return false;
        }
        return true;
    }
    
    /**
     * Returns a country of customer
     * @return string 
     */
    public function getDotCountry() {
        $country = new Country((int)($this->address->id_country));
        return $country->iso_code;
    }
    
    /**
     * Returns an URL to Blik channel logo
     * @return string
     */
    public function getDotBlikLogo() {
        return $this->module->getPath().'web/img/BLIK.png';
    }
    
    /**
     * Returns an URL to MasterPass channel logo
     * @return string
     */
    public function getDotMasterPassLogo() {
        return $this->module->getPath().'web/img/MasterPass.png';
    }
    
    /**
     * Returns an URL to One click card channel logo
     * @return string
     */
    public function getDotOneClickLogo() {
        return $this->module->getPath().'web/img/oneclick.png';
    }
    
    /**
     * Returns an URL to PV card channel logo
     * @return string
     */
    public function getDotPVLogo() {
        return $this->module->getPath().'web/img/oneclick.png';
    }
    
    /**
     * Returns an URL to card channel logo
     * @return string
     */
    public function getDotCreditCardLogo() {
        return $this->module->getPath().'web/img/oneclick.png';
    }
    
    /**
     * Returns an URL to main channel logo
     * @return string
     */
    public function getDotpayLogo() {
        return $this->module->getPath().'web/img/dotpay.png';
    }
    
    /**
     * Returns URL of site where is creating an request to Dotpay
     * @return string
     */
    public function getPreparingUrl() {
        return $this->context->link->getModuleLink($this->module->name,'preparing',array(), $this->module->isSSLEnabled());
    }
    
    /**
     * Init personal data about cart, customer adn adress
     */
    protected function initPersonalData() {
        if($this->context->cart==NULL)
            $this->context->cart = new Cart($this->context->cookie->id_cart);
        
        $this->address = new Address($this->context->cart->id_address_invoice);
        $this->customer = new Customer($this->context->cart->id_customer);
    }
    
    /**
     * Checks, if given currenncy is on the given list, if none of pcurrencies is given as an argument, then it's got from current order settings
     * @param array $allowCurrencyForm
     * @param string|null $paymentCurrency
     * @return boolean
     */
    public function isDotSelectedCurrency($allowCurrencyForm, $paymentCurrency=NULL) {
        $result = false;
        if($paymentCurrency==NULL)
            $paymentCurrency = $this->getDotCurrency();
        $allowCurrency = str_replace(';', ',', $allowCurrencyForm);
        $allowCurrency = strtoupper(str_replace(' ', '', $allowCurrency));
        $allowCurrencyArray =  explode(",",trim($allowCurrency));
        
        if(in_array(strtoupper($paymentCurrency), $allowCurrencyArray)) {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Check, if Virtual Product from Dotpay additional payment is in card
     * @return boolean
     */
    protected function isExVPinCart() {
        $products = $this->context->cart->getProducts(true);
        foreach($products as $product) {
            if($product['id_product'] == $this->config->getDotpayExchVPid())
                return true;
        }
        return false;
    }
}
