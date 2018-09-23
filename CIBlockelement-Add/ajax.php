<?
define('STOP_STATISTICS', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$GLOBALS['APPLICATION']->RestartBuffer();
 
use Bitrix\Main\Loader;
Loader::includeModule("iblock");

if($_GET['action'] == 'createRequest') {
    $IBLOCK = 1;
    /* Делаем запись в инфоблок */
    $arFields = array(
        "ACTIVE" => "Y",
        "IBLOCK_ID" => $IBLOCK,
	"DATE_ACTIVE_FROM" => ConvertTimeStamp(false, "FULL");
        "NAME" => 'Элемент № '.$_GET['NAME'],
    );
    $oElement = new CIBlockElement();
    $idElement = $oElement->Add($arFields, false, false, true);
    /* Свойства */	
		foreach($_GET as $key => $property)
		{
			if($key != 'requestID' && $key != 'action') {
				CIBlockElement::SetPropertyValueCode($idElement, $key, $property); 		
			}
		
		}
	/* Свойства */
}
	
die();?>
