<?php

/**
 * This file is part of the savitskyi-hub/bx-helpers package.
 *
 * (c) Andrew Savitskyi <admin@savitskyi.com.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SavitskyiHub\BxHelpers\Helpers\Highload;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Data\StaticHtmlCache;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\File;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use SavitskyiHub\BxHelpers\Helpers\ClassTrait;
use SavitskyiHub\BxHelpers\Helpers\IO\Dir;
use SavitskyiHub\BxHelpers\Helpers\Main\Variable;

/**
 * Class HandbookSprite
 * @package SavitskyiHub\BxHelpers\Helpers\Highload
 *
 * Класс предназначен для создания глобального спрайта в который будут входить изображения со свойств справочников
 */
class HandbookSprite
{
	use ClassTrait;
	
	/**
	 * Приватные параметры кеша
	 */
	private static $cacheTime = 600;
	private static $cacheDir = '_helpers_handbook_sprite';
	
	/**
	 * Карта поиска свойств для обработки (должна передаваться через другую область видимости, иначе работа функционала будет недоступной)
	 * $key - ключ == идентификатору информационного блока;
	 * $value - значение == массив допустимых свойств;
	 *
	 * @var array
	 */
	private $arMapFind = [];
	
	/**
	 * Список справочников полученных из выборки (для обработчиков)
	 */
	private $arEntityList = [];
	
	/**
	 * Список полученных файлов из справочников
	 * @var array
	 */
	private $arFiles = [];
	
	/**
	 * Список доступных типов файлов
	 * @var array
	 */
	private $arTypeFiles = ["image/jpeg", "image/png"];
	
	/**
	 * Для подсчета максимальной ширины спрайта
	 * @var int
	 */
	private $maxWidthImageSprite = 0;
	
	/**
	 * Для подсчета максимальной высоты спрайта
	 * @var int
	 */
	private $maxHeightImageSprite = 0;
	
	/**
	 * Размер отступа *px
	 * @var int
	 */
	private $padding = 3;
	
	/**
	 * Путь к директории где будет сохранен спрайт и CSS стили
	 * @var string
	 */
	private $pathImgSave = '/upload/helpers/handbook_sprite/';
	private $pathCssSave = '/upload/helpers/handbook_sprite/';
	
	/**
	 * HandbookSprite constructor.
	 * @param bool $stopAfterFindMap
	 */
	public function __construct(bool $stopAfterFindMap = false) {
		try {
			Loader::includeModule("main");
			Loader::includeModule("highloadblock");
			
			if (!class_exists('\Local\Highload\HandbookSprite')) {
				return false;
			}
			
			$this->arMapFind = \Local\Highload\HandbookSprite::$arMapFind;
			$this->arFiles = $this->getFilesByIBlockPropDirectory();
			
			/**
			 * Остановка дальнейшей работы (нужно лишь для получения списка сущностей)
			 */
			if ($stopAfterFindMap) {
				return false;
			}
			
			if (!$this->arFiles) {
				throw new SystemException('Не удалось обнаружить файлы для генерации спрайта');
			}
			
			$this->checkFiles();
			
			if (!$this->arFiles) {
				throw new SystemException('После проверки, файлы для генерации спрайта не обнаружены');
			}
			
			$this->createSprite();
		} catch (SystemException $e) {
			// В дебаг ( переработать в библиотеке момент!!! )
			echo $e->getMessage();
		}
	}
	
	/**
	 * Возвращает список всех файлов во всех справочниках
	 *
	 * @return array
	 * @throws SystemException
	 */
	private function getFilesByIBlockPropDirectory() {
		$arReturn = [];
		
		if (!$this->arMapFind) {
			throw new SystemException('Необходимо сформировать карту поиска');
		}
		
		foreach ($this->arMapFind as $IBlockID => $arProps) {
			$rsIBlockProp = PropertyTable::getList([
				"filter" => [
					"!USER_TYPE_SETTINGS" => "",
					"=IBLOCK_ID" => $IBlockID,
					"=ACTIVE" => "Y",
					"=CODE" => $arProps
				],
				"select" => ["ID", "IBLOCK_ID", "NAME", "CODE", "USER_TYPE_SETTINGS_LIST"]
			]);
			
			if ($arIBlockProp = $rsIBlockProp->fetchAll()) {
				$arHLBlockTables = array_column(array_column($arIBlockProp, 'USER_TYPE_SETTINGS_LIST'), "TABLE_NAME");
				$rsHLBlockClass = HighloadBlockTable::getList(["filter" => ['TABLE_NAME' => $arHLBlockTables]]);
				
				if ($arHLBlockClass = $rsHLBlockClass->fetchAll()) {
					foreach ($arHLBlockClass as $arHLBlock) {
						$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
						$strEntityDataClass = $obEntity->getDataClass();
						
						$rsItemsDirectory = $strEntityDataClass::getList([
							"filter" => [
								"!UF_FILE" => ""
							],
							"order" => [
								'UF_SORT' => 'ASC'
							],
							"select" => ["ID", "UF_SORT", "UF_XML_ID", "UF_FILE"]
						]);
						
						if ($rsItemsDirectory && $rsItemsDirectory->getSelectedRowsCount()) {
							while ($arItemDirectory = $rsItemsDirectory->fetch()) {
								$key = 'ib'.$IBlockID.'-'.$arHLBlock['TABLE_NAME'].'-'.$arItemDirectory["UF_XML_ID"];
								$pathImage = Application::getDocumentRoot().\CFile::GetPath($arItemDirectory["UF_FILE"]);
								
								$arReturn[$key] = $pathImage;
							}
						}
						
						/**
						 * Для обработчиков
						 */
						$this->arEntityList[$arHLBlock["ID"]] = $arHLBlock["NAME"];
					}
				}
			}
		}
		
		return $arReturn;
	}
	
	/**
	 * Проверка файлов на соответствие типа
	 */
	private function checkFiles() {
		foreach ($this->arFiles as $key => $path) {
			$obFile = new File($path);
			
			if (!in_array($obFile->getContentType(), $this->arTypeFiles)) {
				unset($this->arFiles[$key]);
			}
		}
	}
	
	/**
	 * Генерация спрайта. На выходе получим изображение и файл с оформлением CSS стилей
	 */
	private function createSprite() {
		/**
		 * Считаем предварительно размеры спрайта
		 */
		$this->maxHeightImageSprite = (count($this->arFiles) - 1) * $this->padding;
		
		foreach ($this->arFiles as $key => $path) {
			$output = getimagesize($path);
			
			$this->maxHeightImageSprite += $output[1];
			$this->maxWidthImageSprite = ($this->maxWidthImageSprite < $output[0]? $output[0] : $this->maxWidthImageSprite);
		}
		
		/**
		 * Создаем каркас для спрайта
		 */
		$sprite = imagecreatetruecolor($this->maxWidthImageSprite, $this->maxHeightImageSprite);
		
		/**
		 * Добавляем прозрачность
		 */
		imagesavealpha($sprite, true);
		imagefill($sprite, 0, 0, imagecolorallocatealpha($sprite, 0, 0, 0, 127));
		
		/**
		 * Создаем CSS файл
		 */
		$absoluteSpritePath = Application::getDocumentRoot().$this->pathImgSave.'sprite.png';
		$relativeSpritePath = $this->pathImgSave.'sprite.png?fv='.time();
		
		$absoluteCSSPath = Application::getDocumentRoot().$this->pathCssSave.'sprite.css';
		$absoluteCCSMinPath = Application::getDocumentRoot().$this->pathCssSave.'sprite.min.css';
		
		$fileCss = fopen($absoluteCSSPath, 'w');
		$fileCssMin = fopen($absoluteCCSMinPath, 'w');
		
		fwrite(
			$fileCss,
			'.icon-pd {display: -webkit-flex; display: -ms-flexbox; display: flex;}'."\n".
			'.icon-pd::before {content: ""; display: inline-block; background-image: url('.$relativeSpritePath.'); background-repeat: no-repeat;}'."\n"
		);
		
		fwrite(
			$fileCssMin,
			'.icon-pd {display:-webkit-flex;display:-ms-flexbox;display:flex}.icon-pd::before{content:"";display:inline-block;background-image:url('.$relativeSpritePath.');background-repeat:no-repeat}'
		);
		
		/**
		 * Смещение по оси Y для нового изображение
		 */
		$cY = 0;
		
		/**
		 * Формирование спрайта и стилей
		 */
		foreach ($this->arFiles as $key => $path) {
			$output = getimagesize($path);
			$image = ($output["mime"] == "image/png"? imagecreatefrompng($path) : imagecreatefromjpeg($path));
			
			/**
			 * Копируем исходное изображение на спрайт
			 */
			imagecopy($sprite, $image, 0, $cY, 0, 0, $output[0], $output[1]);
			
			/**
			 * Дописываем стили для каждого изображения
			 */
			$pY = round(($cY / ($this->maxHeightImageSprite - $output[1]) * 100), 4).'%';
			
			$xZ = round(($this->maxWidthImageSprite / $output[0] * 100), 4).'%';
			$yZ = round(($this->maxHeightImageSprite / $output[1] * 100), 4).'%';
			
			fwrite($fileCss, '.icon-pd.'.$key.'::before {background-position: 0% '.$pY.'; background-size: '.$xZ.' '.$yZ.'}'."\n");
			fwrite($fileCssMin, '.icon-pd.'.$key.'::before{background-position:0% '.$pY.';background-size:'.$xZ.' '.$yZ.'}');
			
			/**
			 * Увеличиваем смещение
			 */
			$cY += $output[1] + $this->padding;
		}
		
		/**
		 * Сохраняем СSS
		 */
		fclose($fileCss);
		fclose($fileCssMin);
		
		/**
		 * Сохраняем спрайт
		 */
		imagepng($sprite, $absoluteSpritePath);
		imagedestroy($sprite);
	}
	
	/**
	 * Создание обработчиков
	 */
	public static function createEvents() {
		Variable::getInstance();
		
		if (Variable::$bxRequest->isAdminSection()) {
			$сache = Cache::createInstance();
			$сacheTime = self::$cacheTime;
			$cacheDir = Dir::getCacheDirectoryPrefixName().self::$cacheDir;
			
			if ($сache->initCache($сacheTime, SITE_ID, $cacheDir)) {
				$arEntityList = $сache->getVars()["arEntityList"];
			} elseif ($сache->startDataCache()) {
				$arEntityList = (new HandbookSprite(true))->arEntityList;
				
				$сache->endDataCache([
					"arEntityList" => $arEntityList
				]);
			}
			
			/**
			 * В случаи если присутствуют нужные сущности
			 */
			if ($arEntityList) {
				$eventManager = EventManager::getInstance();
				
				foreach ($arEntityList as $entityName) {
					$eventManager->addEventHandler('', $entityName.'OnAfterAdd', ['\SavitskyiHub\BxHelpers\Helpers\Highload\HandbookSprite', 'Init']);
					$eventManager->addEventHandler('', $entityName.'OnAfterDelete', ['\SavitskyiHub\BxHelpers\Helpers\Highload\HandbookSprite', 'Init']);
					$eventManager->addEventHandler('', $entityName.'OnAfterUpdate', ['\SavitskyiHub\BxHelpers\Helpers\Highload\HandbookSprite', 'Init']);
				}
			}
		}
	}
	
	/**
	 * Инициализирует работу функционала (через обработчики, напрямую не запускать этот метод)
	 */
	public static function Init(\Bitrix\Main\Entity\Event $event) {
		if (Variable::$bxRequest->isAdminSection()) {
			new HandbookSprite();
			
			/**
			 * При пересоздании файлов очищаем композитный кеш, чтобы стили не указывали на устарелый файл стилей
			 */
			StaticHtmlCache::getInstance()->deleteAll();
		}
	}
}


