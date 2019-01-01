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

use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

/**
 * Class BeforeProlog
 * @package SavitskyiHub\BxHelpers\Helpers
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 */
class BeforeProlog
{
	/**
	 * - инициализирует все конструкторы, чтобы весь функционал библиотеки уже был доступен для использования;
	 * - метод автоматически выполняться через обработчик в прологе ядра;
	 */
    public static function Init() {
    	new Variable;
        //new User;
    }
}