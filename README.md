# bx-helpers
Вспомогательная библиотека для удобной разработки и поддержки проекта на платформе 1С-Bitrix

## Установка/Настройка

1) Компосер ...

2) Перейти на страницу командной строки в административной панели и запустить процесс инсталяции необходимых зависимостей:

```php
use SavitskyiHub\BxHelpers\Helpers\Install\Mail_Install_Highload;
use SavitskyiHub\BxHelpers\Helpers\Install\User_Group_Install;

new Mail_Install_Highload("УКАЗАТЬ_ПРЕФИКС");
new User_Group_Install();
```

3) Скинуть полностью кэш в разделе `Настройки -> Настройки продукта -> Автокеширование -> Очистка файлов кеша` выбрать "Все" и нажать "Начать";

4) Для почтовых событий установить получателей:

- перейти в раздел `Настройки -> Настройки продукта -> Почтовые события -> Почтовые шаблоны`;
- 

5) Проверить отправку писем и логов:

-
-

6) В файле `init.php` подключить следующий код:

```php
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;

// Include Autoload
if (file_exists(Application::getDocumentRoot().'/local/library/vendor/autoload.php')) {
 require_once(Application::getDocumentRoot().'/local/library/vendor/autoload.php');
	
	if (class_exists('\SavitskyiHub\BxHelpers\Helpers\BeforeProlog')) {
		EventManager::getInstance()->addEventHandler('main', 'OnBeforeProlog', ['\SavitskyiHub\BxHelpers\Helpers\BeforeProlog', 'Init']);
	}
}
```





7) Подключить стили и скрипты

8) картинкы

9) Проверить отправку письма администрации

//use SavitskyiHub\BxHelpers\Helpers\Mail\Send;
//Send::Admin('test asdas dasdasdas test test', 'ERROR');






## Удаление

1) Выполнить команды

 use SavitskyiHub\BxHelpers\Helpers\Install\Mail_Uninstall_Highload;
   new Mail_Uninstall_Highload("savitskyi");
   
    use SavitskyiHub\BxHelpers\Helpers\Install\User_Group_Uninstall;
   new User_Group_Uninstall();
   
2)   В init.php удалить код подвключения что указан в установке:

3) Отключить подключения стилей и скриптов у себя в шаблоне
