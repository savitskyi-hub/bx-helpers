<?php

define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", false);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;
use SavitskyiHub\BxHelpers\Helpers\Main\Method;
use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

try {
	if (!defined('B_PROLOG_INCLUDED') || !B_PROLOG_INCLUDED) {
		throw new SystemException('Missing kernel connection');
	}
	
	Variable::getInstance();
	
	if (!($mode = Variable::$bxRequest->getPost('mode'))) {
		throw new SystemException('Not mode in ajax request');
	}
	
	switch ($mode) {
		case "GET":
			$captcha = Method::getNewParamsCaptcha();
			$newCaptcha = '
				<div class="helpers-form-captcha">
					<img src="/bitrix/tools/captcha.php?captcha_code='.$captcha["code"].'" alt="CAPTCHA">
					
					<div class="helpers-form-input">
						<div class="helpers-form-field-title"></div>
						<input name="captcha_code" value="'.$captcha["code"].'" type="hidden">
						<input name="captcha_word" type="text" value required autocomplete="off">
						<div class="helpers-form-field-error"></div>
					</div>
				</div>';

			$returnAjax = ['status' => 'success', 'message' => "Operation is successful", 'result' => $newCaptcha];
			break;
		
		case "GET_LIST":
			$arIDs = Variable::$bxRequest->getPost('IDs');
			$arReturnContent = [];
			
			if (!$arIDs) {
				throw new SystemException('List IDs is empty');
			}
			
			foreach ($arIDs as $id) {
				$captcha = Method::getNewParamsCaptcha();
				$arReturnContent[$id] = '
					<div class="helpers-form-captcha">
						<img src="/bitrix/tools/captcha.php?captcha_code='.$captcha["code"].'" alt="CAPTCHA">
						
						<div class="helpers-form-input">
							<div class="helpers-form-field-title"></div>
							<input name="captcha_code" type="hidden" value="'.$captcha["code"].'">
							<input name="captcha_word" type="text" value required autocomplete="off">
							<div class="helpers-form-field-error"></div>
						</div>
					</div>';
			}
			
			$returnAjax = ['status' => 'success', 'message' => "Operation is successful", 'result' => $arReturnContent];
			break;
	}
	
} catch (SystemException $e) {
	$returnAjax = ['status' => 'error', 'message' => $e->getMessage()];
} finally {
	echo Json::encode($returnAjax ?? ['status' => 'error', 'message' => 'Error ajax request']);
	exit;
}