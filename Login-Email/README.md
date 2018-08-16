### Логин = E-mail

В шаблоне делаем кастомизацию поля Логин:
Код
```html
<input type="hidden" name="REGISTER[LOGIN]" value="temp_login">
```
Дальше на событие  OnBeforeUserRegister поставим простую функцию, которая подставляет в логин введеный E-mail.
В /local/php_interface/init.php вставляем следующий код: 
Код

```php
AddEventHandler("main", "OnBeforeUserRegister", Array("CRegistration", "OnBeforeUserRegisterHandler"));
class CRegistration 
{ 
   function OnBeforeUserRegisterHandler(&$arFields) 
    { 
          $arFields["LOGIN"] = $arFields["EMAIL"]; 
    } 
} 


После этого так же нужно убрать Логин из Регистрации.
