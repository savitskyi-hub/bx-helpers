<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers;

use Bitrix\Main\EventManager;
use SavitskyiHub\BxHelpers\Helpers\Main\User;
use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

/**
 * Class BeforeProlog
 * @package SavitskyiHub\BxHelpers\Helpers
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 */
class BeforeProlog
{
	/**
	 * - инициализирует необходимые обработчики событий на сайте;
	 * - инициализирует все конструкторы, чтобы весь функционал библиотеки уже был доступен для использования;
	 * - метод автоматически выполняется через обработчик в прологе ядра;
	 */
	public static function Init() {
		EventManager::getInstance()->addEventHandler('main', 'OnAfterUserUpdate', ['\SavitskyiHub\BxHelpers\Helpers\Main\User', 'setClearCacheVar']);
		EventManager::getInstance()->addEventHandler('main', 'OnProlog', ['\SavitskyiHub\BxHelpers\Helpers\Highload\HandbookSprite', 'createEvents']);
		EventManager::getInstance()->addEventHandler('main', 'OnProlog', ['\SavitskyiHub\BxHelpers\Helpers\BeforeViewContent', 'Init']);
		
		Variable::getInstance();
		User::getInstance();
	}
}