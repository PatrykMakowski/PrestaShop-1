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
*  @author    Dotpay Team <tech@dotpay.pl>
*
*  @copyright Dotpay
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*}



{if !$DOTPAY_CONFIGURATION_OK_OC_MAIN or $DP_TEST_OC_MAIN}
<div class="panel"><div class="dotpay-offer">
    <h3>{l s='Registration' mod='dotpay'}</h3>
    <p>{l s='In response to the market\'s needs Dotpay has been delivering innovative Internet payment services providing the widest e-commerce solution offer for years. The domain is money transfers between a buyer and a merchant within a complex service based on counselling and additional security. Within an offer of Internet payments Dotpay offers over 140 payment channels including: mobile payments, instalments, cash, e-wallets, transfers and credit card payments.' mod='dotpay'}</p>
    <p>{l s='To all new clients who have filled in a form and wish to accept payments we offer promotional conditions:' mod='dotpay'}</p>
    <ul>
        <li><b>1,9%</b> {l s='commission on Internet payments (not less than PLN 0.30) ' mod='dotpay'}</li>
        <li>{l s='instalment payments' mod='dotpay'} <b>{l s='without any commission!' mod='dotpay'}</b></li>
        <li>{l s='an activation fee - only PLN 10' mod='dotpay'}</li>
        <li><b>{l s='without any additional fees' mod='dotpay'}</b> {l s='for refunds and withdrawals!' mod='dotpay'}</li>
    </ul>
    <p>{l s='In short, minimizing effort and work time you will increase your sales possibilities. Do not hesitate and start your account now!' mod='dotpay'}</p>
    <div class="cta-button-container">
        <a href="http://www.dotpay.pl/prestashop/" class="cta-button">{l s='Register now!' mod='dotpay'}</a>
    </div>
</div></div>
{/if}
<div class="panel"><div class="dotpay-config">
	<br>
    <h3>{l s='Configuration' mod='dotpay'}</h3>
    <p>{l s='Thanks to Dotpay payment module the only activities needed for integration are: ID and PIN numbers and URLC confirmation configuration.' mod='dotpay'}</p>
    <p>{l s='ID and PIN can be found in Dotpay panel in Settings in the top bar. ID number is a 6-digit string after # in a "Shop" line.' mod='dotpay'}</p>
    <p>{l s='URLC configuration is just setting an address to which information about payment should be directed. This address is:' mod='dotpay'} <b>{$DP_URLC_OC_MAIN}</b></p>
	<p>{l s='Your shop is going to automatically send URLC address to Dotpay.' mod='dotpay'}</p><br>
    <p><b style="color: brown;">{l s='Only thing You have to do is log in to the Dotpay user panel and untick "Block external URLC" option in Settings -> Notifications -> Urlc configuration -> Edit.' mod='dotpay'}</b></p>
</div></div>

<div class="panel"><div class="dotpay-config-state">
    <h3>{l s='Configuration state' mod='dotpay'}</h3>
	<a href="http://www.dotpay.pl" target="_blank" title="www.dotpay.pl"><img src="{$module_dir_OC_MAIN}img/dotpay_logo85.png" width="85px" height="50px" border="0" /></a>
	
		<h4>{l s='Version of this module is: ' mod='dotpay'}<strong>{$DP_THISMODULE_VERSION_MAIN}</strong>. {l s='Check if there is a newer version of this module on ' mod='dotpay'}
		<a href="https://github.com/dotpay/PrestaShop/releases/latest" target="_blank" title="{l s='Check our repository on the GitHub site.' mod='dotpay'}">{l s='this page' mod='dotpay'}</a>.</h4>
	
    {if $DOTPAY_CONFIGURATION_OK_OC_MAIN}
		<div class="bootstrap">
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h2 style="margin-left: 10px; margin-top: 0px;">{l s='Module is active. ' mod='dotpay'}</h2>
				<br />
				<p style="color: #555;"><b>{l s='If you do not recive payment information, please check URLC configuration in your Dotpay user panel.' mod='dotpay'}</b></p>
				<p style="color: #D27C82;"><b>{if $DP_TEST_OC_MAIN}{l s='Module is in TEST mode. All payment informations are fake!' mod='dotpay'}{/if}</b></p><br><br>
				<p style="color: #D27C82;"><b>{if $is_compatibility_currency_MAIN == '0'}{l s='This version of PrestaShop does not support currencies othen that PLN. Please update your PrestaShop installation to the latest version if you want to use other currencies!' mod='dotpay'}{/if}</b></p>
			</div>
		</div>
    {else}
		<div class="bootstrap">
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h2 style="margin-left: 10px; margin-top: 0px;">{l s='Module is not active. Please check your configuration.' mod='dotpay'}</h2>
				<br />
				<p style="color: #555;"><b>{l s='ID and PIN can be found in Dotpay panel in Settings in the top bar. ID number is a 6-digit string after # in a "Shop" line.' mod='dotpay'}</b></p>
				<br />
			</div>
		</div>
		
		
    {/if}
</div></div>
{literal}

<script type="text/javascript">
		  var badID = '{/literal}{$bad_ID_OC_MAIN}{literal}';
		  var badPIN = '{/literal}{$bad_PIN_OC_MAIN}{literal}';
		  var forcedHTTPS = '{/literal}{$forced_HTTPS_OC_MAIN}{literal}';

	$(document).ready(function(){
				{/literal}{if $is_https_MAIN == '1' AND $DOTPAY_HTTPS_OC_MAIN != '1'}{literal}	
						$("#DP_SSL_OC_MAIN_on").attr('checked', 'checked');
						$("#https_replace").text(forcedHTTPS);
				{/literal}{else}{literal}
						$("#https_replace").text('');
				{/literal}{/if}{literal}
				
				if ($("#DP_TEST_OC_MAIN_on:checked").length == 1){
				$('label[for=DP_TEST_OC_MAIN_on]').css('color', 'red');
				$('#ukryj_test').css('color', 'red');
				$('#ukryj_test_ch').css('color', '#661193');
				}
		

		$('input#DP_ID_OC_MAIN').on('keyup',function(){
		  var charCount = $(this).val().replace(/\s/g, '').length;

			if(charCount == 6){
				if (!($(this).val().match(/[0-9]{4,6}/))){
						$("#infoID").text(badID);
						$("button[name=submitDotpayModule]").prop("disabled", true);
					}else{
						$("#infoID").text("");
						$("button[name=submitDotpayModule]").removeAttr('disabled');	
						$("#ukryj_test").closest("div.form-group").css('display', 'inline');
							$('#ukryj_test').css('display', 'inline');
							$('#ukryj_test_desc').css('display', 'inline');
							$('#DP_TEST_OC_MAIN_on').css('display', 'inline');
							$('#DP_TEST_OC_MAIN_off').css('display', 'inline');
							 $('label[for=DP_TEST_OC_MAIN_off]').css('display', 'inline');
							$('label[for=DP_TEST_OC_MAIN_on]').css('display', 'inline');	
					}
				if (!($(this).val().match(/(\d{6})/))){
							$("#ukryj_test").closest("div.form-group").css('display', 'none');
							$("#ukryj_test_desc").closest("div.form-group").css('display', 'none');
							$("#ukryj_test_ch").closest("div.form-group").css('display', 'none');
							$("#ukryj_test_ch_desc").closest("div.form-group").css('display', 'none');
							$("#DP_ONE_CHANNEL_SELECTED_MAIN").closest("div.form-group").css('display', 'none');
							$('#DP_TEST_OC_MAIN_on').css('display', 'none');
				            $('#DP_TEST_OC_MAIN_off').css('display', 'none');
				            $('label[for=DP_TEST_OC_MAIN_off]').css('display', 'none');
							$('label[for=DP_TEST_OC_MAIN_on]').css('display', 'none');

							$("#ukryj_test_desc").hide("fast");	
							$("#ukryj_test_ch").hide("fast");	
							$("#ukryj_test_ch_desc").hide("fast");	
				}else{

						//if ($("#$DP_SWITCH_MULTI_OC_MAIN_on:checked").length == 1){
						if ($("#DP_CHANNELS_VIEW_MAIN") == 4){
							
							$("#DP_ONE_CHANNEL_SELECTED_MAIN").closest("div.form-group").css('display', 'inline');
							$("#ukryj_test_ch_desc").closest("div.form-group").css('display', 'inline');
							$("#ukryj_test_ch").closest("div.form-group").css('display', 'inline');
							$("#DP_ONE_CHANNEL_SELECTED_MAIN").show("fast");	
							$("#ukryj_test_ch_desc").show("fast");							
							$("#ukryj_test_ch").show("fast");
						}else{
							$("#DP_ONE_CHANNEL_SELECTED_MAIN").closest("div.form-group").css('display', 'none');
							$("#ukryj_test_ch_desc").closest("div.form-group").css('display', 'none');
							$("#ukryj_test_ch").closest("div.form-group").css('display', 'none');
							$("#DP_ONE_CHANNEL_SELECTED_MAIN").hide("fast");	
							$("#ukryj_test_ch_desc").hide("fast");							
							$("#ukryj_test_ch").hide("fast");
						
						
						}
							$("#ukryj_test").closest("div.form-group").css('display', 'inline');
							$('#DP_TEST_OC_MAIN_on').css('display', 'inline');
							$('#DP_TEST_OC_MAIN_off').css('display', 'inline');
							 $('label[for=DP_TEST_OC_MAIN_off]').css('display', 'inline');
							$('label[for=DP_TEST_OC_MAIN_on]').css('display', 'inline');
							$("#ukryj_test").show("fast");	
							$("#ukryj_test").show("fast");	
							$("#ukryj_test_desc").show("fast");								
							$("button[name=submitDotpayModule]").removeAttr('disabled');	
					}	
					
			}else{
				$("#infoID").text(badID);
				$("#ukryj_test").closest("div.form-group").css('display', 'none');
				$('#DP_TEST_OC_MAIN_on').css('display', 'none');
				$('#DP_TEST_OC_MAIN_off').css('display', 'none');
				$('label[for=DP_TEST_OC_MAIN_off]').css('display', 'none');
				$('label[for=DP_TEST_OC_MAIN_on]').css('display', 'none');
				$("#ukryj_test_ch").hide("fast");
				$("#ukryj_test").hide("fast");
				$("#ukryj_test_ch_desc").hide("fast");
				$("#DP_ONE_CHANNEL_SELECTED_MAIN").hide("fast");
				$("#ukryj_test_desc").hide("fast");	
				$("button[name=submitDotpayModule]").prop("disabled", true);				
			}		
		});

		$('input#DP_PIN_OC_MAIN').on('keyup',function(){
			var charCountPIN = $(this).val().replace(/\s/g, '').length;
		
			  if(charCountPIN > 0 && charCountPIN < 33){
				if (!($(this).val().match(/([a-zA-Z0-9]{16,32})/))){
						$("#infoPIN").text(badPIN);
						$("button[name=submitDotpayModule]").prop("disabled", true);
					}else{
						$("#infoPIN").text("");
						$("button[name=submitDotpayModule]").removeAttr('disabled');
					}
				}else{
					$("#infoPIN").text(badPIN);
					$("button[name=submitDotpayModule]").prop("disabled", true);
				}
		});	
	
			
	});
	
	
	
</script>



<style type="text/css">
{/literal}
	{if strlen($DP_ID_OC_MAIN) == '6'}
{literal}
	label[for=DP_TEST_OC_MAIN_off]{display:inline;}
	label[for=DP_TEST_OC_MAIN_on]{display:inline;}
	input[name=DP_TEST]{display:inline;}
	#ukryj_test{display:inline; color: #1B23BC;}
	#ukryj_test_ch{display:inline;}
	#ukryj_test_desc{display:inline; float:left; color: #661193;}
	
{/literal}	
{else}
{literal}
	label[for=DP_TEST_OC_MAIN_off]{display:none;}
	label[for=DP_TEST_OC_MAIN_on]{display:none;}
	input[name=DP_TEST]{display:none;}
	#ukryj_test{display:none; color: #1B23BC;}
	#ukryj_test_ch{display:none;}
	#ukryj_test_desc{display:none;float:left; color: #264AE9;}
{/literal}
{/if}	
{literal}	
	input#DP_ID_OC_MAIN{float:left;}
	input#DP_PIN_OC_MAIN{float:left;}
	#infoID {color:red; margin-left: 80px;}
	#infoPIN {color:red; margin-left: 80px;}
	#https_replace {color:red;}
	#ukryj_test_ch_desc {color:#264AE9;}
</style>

{/literal}
	{if strlen($DP_ID_OC_MAIN) == '6'}
{literal}
			<script>
			$(document).ready(function(){
				$("#ukryj_test").closest("div.form-group").css('display', 'inline');
				$("#DP_ONE_CHANNEL_SELECTED_MAIN").closest("div.form-group").css('display', 'inline');
			});
		</script>
{/literal}	
{else}
{literal}
			<script>
			$(document).ready(function(){
				$("#ukryj_test").closest("div.form-group").css('display', 'none');
				$("#DP_ONE_CHANNEL_SELECTED_MAIN").closest("div.form-group").css('display', 'none');
			});
			</script>
{/literal}
{/if}

{if $DP_CHANNELS_VIEW_MAIN == 4}
{literal}
			<script>
			$(document).ready(function(){
				$("#DP_ONE_CHANNEL_SELECTED_MAIN").closest("div.form-group").css('display', 'inline');
				$("#ukryj_test_ch").closest("div.form-group").css('display', 'inline');
				$("#ukryj_summary_multi").closest("div.form-group").css('display', 'none');
			});
		</script>
{/literal}	
{else}
{literal}
			<script>
			$(document).ready(function(){
				$("#DP_ONE_CHANNEL_SELECTED_MAIN").closest("div.form-group").css('display', 'none');
				$("#ukryj_test_ch").closest("div.form-group").css('display', 'none');
				$("#ukryj_summary_multi").closest("div.form-group").css('display', 'inline');
			});
			</script>
{/literal}
{/if}
