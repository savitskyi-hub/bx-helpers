<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Logs;

/**
 * Interface LogInterface
 * @package SavitskyiHub\BxHelpers\Helpers\Logs
 * @author Andrew Savitskyi <admin@savitskyi.com.ua>
 */
interface LogsInterface
{
	/**
	 * Отправка данных в лог файл
	 *
	 * @return mixed
	 */
	public function push();
	
	/**
	 * Установка значения которое попадает в лог
	 *
	 * @param string $value
	 * @return mixed
	 */
	public function setValue(string $value);
}