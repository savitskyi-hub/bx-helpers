<?php

namespace SavitskyiHub\BxHelpers\Helpers\IBlock;

use Bitrix\Main\Loader;

/**
 * Class Element
 * @package Local\Helpers\IBlock
 *
 * Класс предназначен для получения информации нестандартного функционала который связан с елементами информационных блоков
 */
class Element
{
	/**
	 * Получение ссылок на следующий и предыдущий елемент информационного блока с учетом стандартного функционала сортировки елементов
	 * @param int $elementID - ID текущего елемента от которого будет происходить поиск
	 * @param int $sectionID - ID родительського раздела
	 * @param int $iBlockID - ID информационного блока к которому принадлежит текущий элемент
	 * @param string $sortBy_1
	 * @param string $sortBy_2
	 * @param string $orderBy_1
	 * @param string $orderBy_2
	 * @return array
	 */
	public static function getPrevNextElement(
		int $elementID,
		int $sectionID,
		int $iBlockID,
		string $sortBy_1 = "SORT",
		string $sortBy_2 = "ID",
		string $orderBy_1 = "ASC",
		string $orderBy_2 = "DESC"
	): array {
		
		Loader::includeModule("iblock");
		
		$arReturn = $arNav = [];
		$rsNavPrev = \CIBlockElement::GetList(
			[$sortBy_1 => $orderBy_1, $sortBy_2 => $orderBy_2],
			($sectionID?
				["IBLOCK_ID" => $iBlockID, "ACTIVE" => "Y", "IBLOCK_SECTION_ID" => $sectionID]
				:
				["IBLOCK_ID" => $iBlockID, "ACTIVE" => "Y"]
			),
			false,
			["nPageSize" => 1, "nElementID" => $elementID]
		);
		
		if ($rsNavPrev && $rsNavPrev->result->num_rows) {
			while ($arItem = $rsNavPrev->GetNext()) {
				$arNav[] = [
					"ID" => $arItem["ID"],
					"NAME" => $arItem["NAME"],
					"CODE" => $arItem["CODE"],
					"DETAIL_PAGE_URL" => $arItem["DETAIL_PAGE_URL"]
				];
			}
		}
		
		/**
		 * Определяем следующий и предыдущий элемент
		 */
		if (count($arNav) > 2) {
			$arReturn["PREV"] = current($arNav);
			$arReturn["NEXT"] = end($arNav);
		} elseif (count($arNav) > 1) {
			
			if (current($arNav)["ID"] == $elementID) {
				$arReturn["PREV"] = false;
				$arReturn["NEXT"] = end($arNav);
			} else {
				$arReturn["PREV"] = current($arNav);
				$arReturn["NEXT"] = false;
			}
			
		} else {
			$arReturn["PREV"] = false;
			$arReturn["NEXT"] = false;
		}

		return $arReturn;
	}
}