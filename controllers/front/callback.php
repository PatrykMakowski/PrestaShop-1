<?php
/**
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
*/

require_once(DOTPAY_PLUGIN_DIR.'/controllers/front/dotpay.php');

/**
 * Controller for handling callback from Dotpay
 */
class dotpaycallbackModuleFrontController extends DotpayController
{
    /**
     * Defined IP address of localhost
     */
    const LOCAL_IP = '127.0.0.1';
    
    /**
     * Confirm payment based on Dotpay URLC
     */
    public function displayAjax()
    {
        $sellerApiCallback = new DotpaySellerApi($this->config->getDotpaySellerApiUrl());
        if ($_SERVER['REMOTE_ADDR'] == $this->config->getOfficeIp() && $_SERVER['REQUEST_METHOD'] == 'GET') {
            die("--- Dotpay PrestaShop ---"."<br>".
                "Active: ".(int)$this->config->isDotpayEnabled()."<br><br>".
                "--- System Info ---"."<br>".
                "PrestaShop Version: ". _PS_VERSION_ ."<br>".
                "Module Version: ".$this->module->version."<br>".
                "PHP Version: ".PHP_VERSION."<br>".
                "SSL: ".(int)Configuration::get('PS_SSL_ENABLED')."<br>".
                "SSL EVERYWHERE: ".(int)Configuration::get('PS_SSL_ENABLED_EVERYWHERE')."<br><br>".
                "--- Dotpay PLN ---"."<br>".
                "ID: ".$this->config->getDotpayId()."<br>".
                "ID Correct: ".(int)$this->api->checkSellerId($this->config->getDotpayId())."<br>".
                "API Version: ".$this->config->getDotpayApiVersion()."<br>".
                "Test Mode: ".(int)$this->config->isDotpayTestMode()."<br>".
                "Widget: ".(int)$this->config->isDotpayWidgetMode()."<br>".
                "Payment Renew: ".(int)$this->config->isDotpayRenewEn()."<br>".
				"Payment Renew Days: ".(int)$this->config->getDotpayRenewDays()."<br>".
                "Refund: ".(int)$this->config->isDotpayRefundEn()."<br>".
                "Register Order: ".(int)$this->config->isDotpayDispInstruction()."<br>".
                "Disabled Currencies: ".$this->config->getDotpayWidgetDisCurr()."<br><br>".
                "--- Separate Channels ---"."<br>".
                "Credit Card: ".(int)$this->config->isDotpayCreditCard()."<br>".
                "MasterPass: ".(int)$this->config->isDotpayMasterPass()."<br>".
                "Blik: ".(int)$this->config->isDotpayBlik()."<br>".
                "One Click: ".(int)$this->config->isDotpayOneClick()."<br><br>".
                "--- Dotpay PV ---"."<br>".
                "PV Mode: ".(int)$this->config->isDotpayPV()."<br>".
                "PV ID: ".$this->config->getDotpayPvId()."<br>".
                "PV Currencies: ".$this->config->getDotpayPvCurrencies()."<br><br>".
                "--- Dotpay API ---"."<br>".
                "Login: ".$this->config->getDotpayApiUsername()."<br>".
                "Password Correct: ".var_export($sellerApiCallback->isAccountRight($this->config->getDotpayApiUsername(), $this->config->getDotpayApiPassword(), $this->config->getDotpayApiVersion()), true)."<br><br>".
                "--- Dotpay Fee ---"."<br>".
                "Fee Enabled: ".(int)$this->config->getDotpayExCh()."<br>".
                "Fee Flat: ".$this->config->getDotpayExAmount()."<br>".
                "Fee Percentage: ".$this->config->getDotpayExPercentage()."<br><br>".
                "--- Dotpay Discount ---"."<br>".
                "Discount Enabled: ".(int)$this->config->getDotpayDiscount()."<br>".
                "Discount Flat: ".$this->config->getDotpayDiscAmount()."<br>".
                "Discount Percentage: ".$this->config->getDotpayDiscPercentage()
            );
        }
        if (
            !($_SERVER['REMOTE_ADDR'] == $this->config->getDotpayIp() ||
                ($this->config->isDotpayTestMode() &&
                 ($_SERVER['REMOTE_ADDR'] == $this->config->getOfficeIp() ||
                  $_SERVER['REMOTE_ADDR'] == self::LOCAL_IP
                 )
                )
            )
        ) {
            die("PrestaShop - ERROR (REMOTE ADDRESS: ".$_SERVER['REMOTE_ADDR'].")");
        }

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            die("PrestaShop - ERROR (METHOD <> POST)");
        }
        
        if (!$this->api->checkConfirm()) {
            die("PrestaShop - ERROR SIGNATURE - CHECK PIN");
        }
        
        $api = $this->api;
        if ($api->getOperationType() == $api::PAYMENT_OPERATION) {
            $this->makePayment();
        } elseif ($api->getOperationType() == $api::REFUND_OPERATION) {
            $this->makeRefund();
        } else {
            die('PrestaShop - ERROR STATUS');
        }
    }
    
    /**
     * Function which is used to making payments
     */
    private function makePayment()
    {
        $id = ($this->api->isSelectedPvChannel())?$this->config->getDotpayPvId():$this->config->getDotpayId();
        if (Tools::getValue('id') != $id) {
            die("PrestaShop - ERROR ID");
        }
        
        $order = new Order((int)$this->getDotControl(Tools::getValue('control')));
        $currency = new Currency($order->id_currency);
        
        $receivedCurrency = $this->api->getOperationCurrency();
        $orderCurrency = $currency->iso_code;
        
        if ($receivedCurrency != $orderCurrency) {
            die('PrestaShop - NO MATCH OR WRONG CURRENCY - '.$receivedCurrency.' <> '.$orderCurrency);
        }
        
        $receivedAmount = (float)$this->api->getTotalAmount();
        $orderAmount = Tools::displayPrice($order->total_paid, $currency, false);
        $orderAmount = (float)$this->getCorrectAmount(
            preg_replace("/[^-0-9\.]/", '', str_replace(',', '.', $orderAmount))
        );
        
        if ($receivedAmount != $orderAmount) {
            die('PrestaShop - NO MATCH OR WRONG AMOUNT - '.$receivedAmount.' <> '.$orderAmount);
        }
        
        $newOrderState = $this->api->getNewOrderState();
        if ($newOrderState===null) {
            die ('PrestaShop - WRONG TRANSACTION STATUS');
        }
        
        $cc = DotpayCreditCard::getCreditCardByOrder($order->id);
        if ($cc !== null && $cc->id !== null && $cc->card_id == null) {
            $sellerApi = new DotpaySellerApi($this->config->getDotpaySellerApiUrl());
            $ccInfo = $sellerApi->getCreditCardInfo(
                $this->config->getDotpayApiUsername(),
                $this->config->getDotpayApiPassword(),
                $this->api->getOperationNumber()
            );
            $cc->brand = $ccInfo->brand->name;
            $cc->mask = $ccInfo->masked_number;
            $cc->card_id = $ccInfo->id;
            $cc->save();
            $brand = new DotpayCardBrand($ccInfo->brand->name);
            $brand->name = $ccInfo->brand->name;
            $brand->image = $ccInfo->brand->logo;
            $brand->save();
        }
        
        $history = new OrderHistory();
        $history->id_order = $order->id;
        $lastOrderState = OrderHistory::getLastOrderState($history->id_order);
        if ($lastOrderState->id == _PS_OS_PAYMENT_) {
            die('OK');
        }
        if ($lastOrderState->id != $newOrderState) {
            $history->changeIdOrderState($newOrderState, $history->id_order);
            $history->addWithemail(true);
            if ($newOrderState == _PS_OS_PAYMENT_) {
                $payments = OrderPayment::getByOrderId($order->id);
                $numberOfPayments = count($payments);
                if ($numberOfPayments >= 1) {
                    if (empty($payments[$numberOfPayments - 1]->transaction_id)) {
                        $payments[$numberOfPayments - 1]->transaction_id = $this->api->getOperationNumber();
                        $payments[$numberOfPayments - 1]->payment_method = $this->module->displayName;
                        $payments[$numberOfPayments - 1]->update();
                    } else {
                        $payment = $this->prepareOrderPayment($order);
                        $payment->add();
                    }
                }
                $instruction = DotpayInstruction::getByOrderId($order->id);
                if ($instruction !== null) {
                    $instruction->delete();
                }
            }
        } else {
            die ('PrestaShop - THIS STATE ('.$lastOrderState->name.') IS ALERADY REGISTERED');
        }
        die('OK');
    }
    
    /**
     * Function which is used to making refunds
     */
    private function makeRefund()
    {
        $api = $this->api;
        $statusName = $this->api->getOperationStatusName();
        if ($statusName != $api::OPERATION_COMPLETED && $statusName != $api::OPERATION_REJECTED) {
            die('OK');
        }
        
        $order = new Order((int)$this->getDotControl(Tools::getValue('control')));
        
        $payments = OrderPayment::getByOrderId($order->id);
        $foundPaymet = false;
        $sumOfPayments = 0.0;
        foreach ($payments as $payment) {
            if ($payment->transaction_id == $this->api->getOperationNumber()) {
                die('PrestaShop - PAYMENT '.$this->api->getOperationNumber().' IS ALREADY SAVED');
            } elseif ($payment->transaction_id == $this->api->getRelatedOperationNumber()) {
                $foundPaymet = true;
            }
            if ($payment->payment_method == $this->module->displayName) {
                $sumOfPayments += (float)$payment->amount;
            }
        }
        if (!$foundPaymet) {
            die('PrestaShop - PAYMENT '.$this->api->getRelatedOperationNumber().' IS NOT SAVED');
        }
        $receivedAmount = (float)($this->api->getTotalAmount());
        
        if ($receivedAmount - $sumOfPayments >= 0.01) {
            die('PrestaShop - NO MATCH OR WRONG AMOUNT - '.$receivedAmount.' > '.$sumOfPayments);
        }
        
        $lastOrderState = OrderHistory::getLastOrderState($order->id);
        if ($lastOrderState->id != $this->config->getDotpayWaitingRefundStatusId()) {
            die('PrestaShop - REFUND HAVEN\'T BEEN SUBMITTED');
        }
        
        if ($this->api->getOperationStatusName() == $api::OPERATION_COMPLETED) {
            $payment = $this->prepareOrderPayment($order, true);
            $payment->add();

            if ($receivedAmount < $sumOfPayments) {
                $state = $this->config->getDotpayPartialRefundStatusId();
            } else {
                $state = $this->config->getDotpayTotalRefundStatusId();
            }
            
            $history = new OrderHistory();
            $history->id_order = $order->id;
            $history->changeIdOrderState($state, $history->id_order);
            $history->addWithemail(true);
        } elseif ($this->api->getOperationStatusName() == $api::OPERATION_REJECTED) {
            $state = $this->config->getDotpayFailedRefundStatusId();
            $history = new OrderHistory();
            $history->id_order = $order->id;
            $history->changeIdOrderState($state, $history->id_order);
            $history->addWithemail(true);
        }
        die('OK');
    }
    
    /**
     * Creates and prepares payment for given order
     * @param Order $order Order object
     * @param bool $minus Flag, if minus sign should be set
     */
    private function prepareOrderPayment($order, $minus = false) {
        $payment = new OrderPayment();
        $payment->order_reference = $order->reference;
        $payment->amount = (float)(($minus ? '-':'').Tools::getValue('operation_original_amount'));
        $payment->id_currency = $order->id_currency;
        $payment->conversion_rate = 1;
        $payment->transaction_id = $this->api->getOperationNumber();
        $payment->payment_method = $this->module->displayName;
        $payment->date_add = new \DateTime();
        return $payment;
    }
    
    /**
     * Returns a correct and well-formatted amount, which is based on input parameter
     * @param float $amount Amount of order
     * @return float
     */
    private function getCorrectAmount($amount)
    {
        $count = 0;
        do {
            $amount = preg_replace("/(\d+)\.(\d{3,})/", "$1$2", $amount, -1, $count);
        } while ($count > 0);
        return $amount;
    }
}
