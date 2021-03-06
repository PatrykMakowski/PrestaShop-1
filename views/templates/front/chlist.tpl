{*
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
*}
{literal}
    <script type="text/javascript" language="JavaScript">
        window.dotpayConfig = {
            "isWidget": Boolean({/literal}{$isWidget|escape:'htmlall':'UTF-8'}{literal})
        };
    </script>
{/literal}

{if $inCheckout}
<link rel="stylesheet" href="{$modules_dir|escape:'htmlall':'UTF-8'}dotpay/views/css/front.css" type="text/css" media="all" />
{include file='./scripts/noConflict.tpl'}
{/if}

{include file='./scripts/jquery.transit.tpl'}
{include file='./scripts/payment.tpl'}

{if $goodCurency }
    
    {if $isWidget}
        <link href="{$dotpayUrl|escape:'htmlall':'UTF-8'}widget/payment_widget.min.css" rel="stylesheet">
        <script type="text/javascript">
        {literal}
            var dotpayWidgetConfig = {
                sellerAccountId: {/literal}{$userId|escape:'htmlall':'UTF-8'}{literal},
                amount: {/literal}{$amount|escape:'htmlall':'UTF-8'}{literal},
                currency: '{/literal}{$currency|escape:'htmlall':'UTF-8'}{literal}',
                lang: '{/literal}{$lang|escape:'htmlall':'UTF-8'}{literal}',
                widgetFormContainerClass: 'my-form-widget-container',
                offlineChannel: 'mark',
                offlineChannelTooltip: true,
                disabledChannels: [{/literal}{$disabledChannels|escape:'htmlall':'UTF-8'}{literal}],
                host: '{/literal}{$channelApiUrl}{literal}'
            };
        {/literal}
        </script>
        {include file='./scripts/payment_widget.tpl'}
    {/if}
    
    {foreach from=$channelList key=dotpaySingleChannel item=dotpaySingleForm}
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <p class="dotpay_unsigned_channel payment_module">
                <a class="dotpay dropbtn"{if $directPayment and $dotpaySingleChannel=='dotpay'} data-type="dotpay_payment_link"{/if}>
                    <label display-cell form-target="{$dotpaySingleChannel|escape:'htmlall':'UTF-8'}">
                        <img class="{$dotpaySingleChannel|escape:'htmlall':'UTF-8'}" src="{$dotpaySingleForm['image']}">
                        {$dotpaySingleForm['description']}
                    </label>
                </a>
                <div class="dotpay-channels-list">
                    {if $exAmount > 0}
                        <p class="alert alert-danger">{$exMessage|escape:'htmlall':'UTF-8'}: {$exAmount|escape:'htmlall':'UTF-8'}&nbsp;{$currency|escape:'htmlall':'UTF-8'}.</p>
                    {/if}

                    {if $discAmount > 0}
                        <p class="alert alert-success">{$discMessage|escape:'htmlall':'UTF-8'}: {$discAmount|escape:'htmlall':'UTF-8'}&nbsp;{$currency|escape:'htmlall':'UTF-8'}.</p>
                    {/if}
                    {dotpayGenerateForm form=$dotpaySingleForm}
                </div>
            </p>
        </div>
    </div>
    {/foreach}
    
{else}
    <p class="alert alert-danger">{l s='Your currency is not yet supported by Dotpay' mod='dotpay'}</p>
{/if}