
Основные функции Bitrix API
================


Вывод title в основном шаблоне сайта.
```php
<?$APPLICATION->ShowTitle()?>
```
      
Подключение для вывода в шаблоне сайта основных полей тега : мета-теги Content-Type, robots, keywords, description; стили CSS; скрипты.
```php
    <?$APPLICATION->ShowHead()?>
```    
Выводит панель управления администратора.
```php
<?$APPLICATION->ShowPanel();?>
```

Подставляет путь к шаблону.
```php
<?=SITE_TEMPLATE_PATH?>
```

> Заголовок (в h1 например использовать).
```php
<?$APPLICATION->ShowTitle(false);?>
```

Получить путь к картинке
```php
CFile::GetPath($arItem["PICTURE"]);
```

ResizeImageGet
```php
$arResult["DETAIL_PICTURE_SMALL"] = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], Array("width" => ШИРИНА, "height" => ВЫСОТА), BX_RESIZE_IMAGE_PROPORTIONAL, false);
```
> BX_RESIZE_IMAGE_PROPORTIONAL - Сохранение пропорций

> BX_RESIZE_IMAGE_EXACT - Cохранение пропорций с обрезанием по заданной ширине и высоте;

> BX_RESIZE_IMAGE_PROPORTIONAL_ALT - масштабирует с сохранением пропорций за ширину при этом принимается максимальное значение из высоты/ширины, размер ограничивается $arSize, улучшенная обработка вертикальных картинок.



> Подключение css и js
```php
$APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH."/css/catalog.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");
```

С помощью d7
```php
use Bitrix\Main\Page\Asset;
Asset:getInstance()->addCss(SITE_TEMPLATE_PATH."/css/catalog.css");
Asset:getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jscript.js");

```


> Функция dump для вывода массивов, видная только админу ( или всем )
```php
function dump($var, $die=false, $all=false)
{
      global $USER;
      if( ($USER->GetID()==1) || ($all==true) )
      {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
      }
      if($die)
      die('hello');
}
```

Отправка почты
```php
/* Отправка письма администратору */
$postTemplate = 92;     // ID Шаблона
$arEventFields = array( // Свойства
    "EMAIL"   => $_POST['email'],
    "FIO"     => $_POST['fio'],
    "PHONE"   => $_POST['phone'],
    "COMMENT" => $_POST['comment']
);
CEvent::Send("ALX_FEEDBACK_FORM", "h1", $arEventFields, $postTemplate);
```

#Улучшаем структуру
> Желаемая структура папки local
```
/local/templates/
/local/php_interface/
/local/php_interface/init.php
/local/php_interface/include - Подключаемые файлы 
/local/include - <i>Включаемые области</i>
/local/css/
/local/js/
/local/ajax/
/local/components/
```
	

 <b>/local/php_interface/init.php</b>
Файл может содержать в себе инициализацию обработчиков событий, подключение дополнительных функций - общие для всех сайтов. Для каждого отдельного сайта может быть свой аналогичный файл. В этом случае он располагается по пути /bitrix/php_interface/ID сайта/init.php

Чтобы init.php не превращался в свалку непонятного кода следует код размещать логически группируя по файлам и классам.
Пример файла <b>init.php</b>:
```php
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//ID инфоблоков
define("IBLOCK_SPECIALITY_ID", 17); //Специальности
define("IBLOCK_APPOINTMENT_ID", 36); //Запись к врачу 
define("IBLOCK_BACK_CALL_ID", 37); //Обратный звонок
//подключение доп файлов
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/include.php")){
	require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/include.php");
}
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/function.php")){
	require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/function.php");
}

//Отправка сообщения пользователю, после регистрации
AddEventHandler("main", "OnAfterUserAdd", "OnAfterUserRegisterHandler");
AddEventHandler("main", "OnAfterUserRegister", "OnAfterUserRegisterHandler");
    function OnAfterUserRegisterHandler(&$arFields)
    {
	   if (intval($arFields["ID"])>0)
	   {
		  $toSend = Array();
		  $toSend["PASS"] = $arFields["CONFIRM_PASSWORD"];
		  $toSend["EMAIL"] = $arFields["EMAIL"];
		  $toSend["LOGIN"] = $arFields["LOGIN"];
		  $toSend["NAME"] = (trim ($arFields["NAME"]) == "")? $toSend["NAME"] = htmlspecialchars('<Не указано>'): $arFields["NAME"];
		  $toSend["LAST_NAME"] = (trim ($arFields["LAST_NAME"]) == "")? $toSend["LAST_NAME"] = htmlspecialchars('<Не указано>'): $arFields["LAST_NAME"];
		  CEvent::Send("USER_REG", SITE_ID, $toSend, "N", 94);
	   }
	   return $arFields;
    }

```
