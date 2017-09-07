
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
