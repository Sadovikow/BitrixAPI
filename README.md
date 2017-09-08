
Основные функции Bitrix API
================


Вывод title в основном шаблоне сайта.
```
<?$APPLICATION->ShowTitle()?>
```
      
Подключение для вывода в шаблоне сайта основных полей тега : мета-теги Content-Type, robots, keywords, description; стили CSS; скрипты.
```
    <?$APPLICATION->ShowHead()?>
```    
Выводит панель управления администратора.
```
<?$APPLICATION->ShowPanel();?>
```

Подставляет путь к шаблону.
```
<?=SITE_TEMPLATE_PATH?>
```

> Заголовок (в h1 например использовать).
```
<?$APPLICATION->ShowTitle(false);?>
```

> Подключение css и js
```
$APPLICATION->SetAdditionalCss(SITE_TEMPLATE_PATH."/css/catalog.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");
```

С помощью d7
```
use Bitrix\Main\Page\Asset
Asset:getInstance()->addCss(SITE_TEMPLATE_PATH."/css/catalog.css");
Asset:getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jscript.js");
```
