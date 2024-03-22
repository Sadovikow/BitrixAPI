# Выгрузка товаров из битрикс с ссылками

Если есть необходимость сделать выгрузку в .csv с URL карточек товара и картинок, ниже работающий скрипт.

Вам нужно просто подставить id каталога в переменную $IBLOCK_ID, и при необходимости в переменную catalogPath путь до каталога, если данный путь отличается от вашего 
```php
<?
global $APPLICATION;
$APPLICATION->RestartBuffer();

CModule::IncludeModule("iblock");

$fileName    = $_SERVER['DOCUMENT_ROOT'] . '/export_links.csv';
$IBLOCK_ID   = 18; // ID инфоблока каталога
$file        = fopen($fileName, "w+");
$a           = ';';
$d           = "\r\n";
$t           .= 'id;article;brand;url;image_url' . $d;
$catalogPath = '/catalog/'; // Путь до корня каталога

$arFilter = [
    "INCLUDE_SUBSECTIONS" => "Y",
    "IBLOCK_TYPE"         => 'catalog',
    "IBLOCK_ID"           => $IBLOCK_ID,
];
function toWindow($ii)
{
    return iconv("utf-8", "windows-1251", $ii);
}

fputs($file, toWindow($t));
$db_list = CIBlockElement::GetList(["ID" => "ASC"], $arFilter, false, false, [
                                                      "PREVIEW_PICTURE",
                                                      "ID",
                                                      "DETAIL_PAGE_URL",
                                                      "XML_ID",
                                                      "NAME",
                                                  ]
);
if (intval($db_list->SelectedRowsCount()) > 0):
    while ($ar_list = $db_list->GetNext()) {
        $i++;
        $t = '';
        $t .= $ar_list['ID'] . $a
            . '"' . $ar_list['XML_ID'] . '"' . $a
            . '"' . str_replace('"', '', htmlspecialcharsBack($ar_list['NAME'])) . '"' . $a
            . str_replace(
                '&amp;',
                '&',
                'https://' . str_replace(
                    ':443',
                    '',
                    $_SERVER['HTTP_HOST']
                ) . $catalogPath . $ar_list['DETAIL_PAGE_URL']
            ) . $a
            . 'https://' . str_replace(':443', '', $_SERVER['HTTP_HOST']) . \CFile::GetPath(
                $ar_list['PREVIEW_PICTURE'] ? $ar_list['PREVIEW_PICTURE'] : $ar_list['DETAIL_PICTURE']
            ) . '' . $d;
        fputs($file, toWindow($t));
    }
endif;
fclose($file);
global $APPLICATION;
$APPLICATION->RestartBuffer();
if (file_exists($fileName)) {
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=" . basename($fileName));
    header('Content-Length: ' . filesize($fileName));
    readfile($fileName);
}
exit;
```
