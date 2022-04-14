
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
©
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

Запрос из инфоблока по элементам
CIBlockElement::<b>GetList</b>
```php

if(count($array > 0) {
	$arraySize = count($array);
	$arSort   = array('DATE_CREATE' => 'DESC');
	$arFilter = Array("IBLOCK_ID"=> IBLOCK_CATALOG_ID, "ID" => $array, "ACTIVE"=>"Y");
	$navParams = Array("nPageSize"=>$arraySize);
	$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_CODE");
	$dbFields = CIBlockElement::GetList($arSort, $arFilter, false, $navParams, $arSelect);
	while($dbElement = $dbFields->GetNextElement())
	{
	   $arFields = $dbElement->GetFields();
	   $arFields[PROPERTIES] = $dbElement->GetProperties(); // Не желательно, нужно пользоваться arSelect property_code
	}
}
```

Запрос из инфоблока по разделам текущего раздела
```php
$rs_Section = CIBlockSection::GetList(array('left_margin' => 'asc'), array('IBLOCK_ID' => 5, 'SECTION_ID' => $arResult['SECTION_ID']));
while ( $arSection = $rs_Section->Fetch() )
{
    $arSections[$arSection['ID']] = $arSection;
}

dump($arSections);
```

Вывести разделы текущего раздела инфоблока
```php
$rs_Section = CIBlockSection::GetList(array('left_margin' => 'asc'), array('IBLOCK_ID' => 5, 'SECTION_ID' => $arResult['SECTION_ID']));
while ( $arSection = $rs_Section->Fetch() )
{
    $arSections[$arSection['ID']] = $arSection;
}

dump($arSections);
```

Изменение свойства инфоблока
CIBlockElement::<b>SetPropertyValuesEx</b>
```php
CIBlockElement::SetPropertyValuesEx($_POST['ELEMENT_ID'], $IBLOCK_ID, Array("CODE" => $_POST['VALUE']) );
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

## Добавление элемента в инфоблок через форму
<a href="https://github.com/Sadovikow/BitrixAPI/tree/master/CIBlockelement-Add">CIBlockelement-Add</a> - Идеальный пример. Реализовано при помощи технологии Ajax.

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


#  Вызов включаемой области - из файла

```php
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"MODE" => "php",
		"PATH" => "/local/include/phone.php"
	)
);?>
```

# Достать информацию о текущем пользователе

```php
	global $USER;
	echo $USER->GetID();
	echo $USER->GetLogin();
	echo $USER->GetFirstName();
```

# Преобразование TIMESTAMP_X в формат Даты

```php

	$dd = $arItem[TIMESTAMP_X];
	$ddd = strtotime($dd);
	echo date("d.m.Y", $ddd);
```

# Количество найденных элементов инфоблока

```php
$arResult["NAV_RESULT"]->SelectedRowsCount();
```

# Количество найденных элементов инфоблока со склонениями

```php
function num2word($num, $words)
{
    $num = $num % 100;
    if ($num > 19) {
        $num = $num % 10;
    }
    switch ($num) {
        case 1: {
            return($words[0]);
        }
        case 2: case 3: case 4: {
            return($words[1]);
        }
        default: {
            return($words[2]);
        }
    }
}?>
	<span class="col">
		<? $APPLICATION->ShowViewContent('count'); ?>
	</span>
<?
$this->SetViewTarget('count');
	$count = $arResult["NAV_RESULT"]->SelectedRowsCount();
	$word = num2word($count, array('товар', 'товара', 'товаров'));
	echo $count.' '.$word;
$this->EndViewTarget();
```

# Всплывающее окно JavaScript функция битрикса

```javascript

var popup = BX.PopupWindowManager.create("popup-message", null, {
content: "Товар добавлен в корзину",
autoHide : true,
offsetTop : 1,
offsetLeft : 0,
lightShadow : true,
closeByEsc : true,
overlay: {
backgroundColor: '000000', opacity: '80'
}
});
popup.show();
var popup = BX.PopupWindowManager.create("popup-message", null, {
    content: "Hello World!",
   darkMode: true,
   autoHide: true
});

popup.show();
content: 'Контент, отображаемый в теле окна',
           width: 400, // ширина окна
           height: 100, // высота окна
           zIndex: 100, // z-index
           closeIcon: {
               // объект со стилями для иконки закрытия, при null - иконки не будет
               opacity: 1
           },
           titleBar: 'Заголовок окна',
           closeByEsc: true, // закрытие окна по esc
           darkMode: false, // окно будет светлым или темным
           autoHide: false, // закрытие при клике вне окна
           draggable: true, // можно двигать или нет
           resizable: true, // можно ресайзить
           min_height: 100, // минимальная высота окна
           min_width: 100, // минимальная ширина окна
           lightShadow: true, // использовать светлую тень у окна
           angle: true, // появится уголок
           overlay: {
               // объект со стилями фона
               backgroundColor: 'black',
               opacity: 500
           },

```	


Проверить отправку почтовых событий
```sql
select * from b_event
order by date_insert desc
```

# Выполнение отложенной функции в шаблоне компонента
```php


Формируется:
$this->SetViewTarget("sub_h1");
...
$this->EndViewTarget();

Вставляется:
$APPLICATION->ShowViewContent('sub_h1');
```


# d7 - Пример
```php

\Bitrix\Main\Loader::includeModule('iblock');


$dbItems = \Bitrix\Iblock\ElementTable::getList(array(
	'order' => array('SORT' => 'ASC'), // сортировка
	'select' => array('ID', 'NAME', 'IBLOCK_ID', 'SORT', 'TAGS'), // выбираемые поля, без свойств. Свойства можно получать на старом ядре \CIBlockElement::getProperty
	'filter' => array('IBLOCK_ID' => 4), // фильтр только по полям элемента, свойства (PROPERTY) использовать нельзя
	'group' => array('TAGS'), // группировка по полю, order должен быть пустой
	'limit' => 1000, // целое число, ограничение выбираемого кол-ва
	'offset' => 0, // целое число, указывающее номер первого столбца в результате
	'count_total' => 1, // дает возможность получить кол-во элементов через метод getCount()
	'runtime' => array(), // массив полей сущности, создающихся динамически
	'data_doubling' => false, // разрешает получение нескольких одинаковых записей
	'cache' => array( // Кеш запроса. Сброс можно сделать методом \Bitrix\Iblock\ElementTable::getEntity()->cleanCache();
		'ttl' => 3600, // Время жизни кеша
		'cache_joins' => true // Кешировать ли выборки с JOIN
	),
));

$dbItems->fetch(); // или $dbItems->fetchRaw() получение одной записи, можно перебрать в цикле while ($arItem = $dbItems->fetch())
$dbItems->fetchAll(); // получение всех записей
$dbItems->getCount(); // кол-во найденных записей без учета limit, доступно если при запросе было указано count_total = 1
$dbItems->getSelectedRowsCount(); // кол-во полученных записей с учетом limit

\Bitrix\Iblock\TypeTable::getList(); // типы инфоблоков
\Bitrix\Iblock\IblockTable::getList(); // инфоблоки
\Bitrix\Iblock\PropertyTable::getList(); // свойства инфоблоков
\Bitrix\Iblock\PropertyEnumerationTable::getList(); // значения свойств, например списков
\Bitrix\Iblock\SectionTable::getList(); // Разделы инфоблоков
\Bitrix\Iblock\ElementTable::getList(); // Элементы инфоблоков 
\Bitrix\Iblock\InheritedPropertyTable::getList(); // Наследуемые свойства (seo шаблоны)

checkFields(Result $result, $primary, array $data) // метод проверяет поля данных перед записью в БД.
getById($id) // получение элемента по ID
getByPrimary($primary, array $parameters = array()) // метод возвращает выборку по первичному ключу сущности и по опциональным параметрам \Bitrix\Main\Entity\DataManager::getList.
getConnectionName() // метод возвращает имя соединения для сущности. 12.0.9
getCount($filter = array(), array $cache = array()) // метод выполняет COUNT запрос к сущности и возвращает результат. 12.0.10
getEntity() // метод возвращает объект сущности.
getList(array $parameters = array()) // получение элементов, подробнее было выше
getMap() // метод возвращает описание карты сущностей. 12.0.7
getRow(array $parameters) // метод возвращает один столбец (или null) по параметрам для \Bitrix\Main\Entity\DataManager::getList.
getRowById($id) // метод возвращает один столбец (или null) по первичному ключу сущности. 14.0.0
getTableName() // метод возвращает имя таблицы БД для сущности. 12.0.7
query() // метод создаёт и возвращает объект запроса для сущности.
enableCrypto($field, $table = null, $mode = true) // метод устанавливает флаг поддержки шифрования для поля. 17.5.14
cryptoEnabled($field, $table = null) // метод возвращает true если шифрование разрешено для поля. 17.5.14
addMulti($rows, $ignoreEvents = false)
updateMulti($primaries, $data, $ignoreEvents = false)
// Следующий методы заблокированы у инфоблоков
add(array $data) // добавление элемента
delete($primary) // удаление элемента по ID
update($primary, array $data) // обновление элемента по ID
```


# d7 - Пример на практике

```php
// Инфоблок и его свойства
$arIblock = \Bitrix\Iblock\IblockTable::getList(array(
	'filter' => array('CODE' => 'news'),
))->fetch();

$arProps = \Bitrix\Iblock\PropertyTable::getList(array(
	'select' => array('*'),
	'filter' => array('IBLOCK_ID' => $arIblock['ID'])
))->fetchAll();

// Значения определенного свойства типа список
$dbEnums = \Bitrix\Iblock\PropertyEnumerationTable::getList(array(
	'order' => array('SORT' => 'asc'),
	'select' => array('*'),
	'filter' => array('PROPERTY_ID' => $arIblockProp['ID'])
));
while($arEnum = $dbEnums->fetch()) {
	$arIblockProp['ENUM_LIST'][$arEnum['ID']] = $arEnum;
}
