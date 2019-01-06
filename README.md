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

3) Скинуть полностью кэш в разделе `Настройки -> Настройки продукта -> Автокеширование -> Очистка файлов кеша` выбрать "**Все**" и нажать "**Начать**";

4) Для почтовых событий установить получателей:

- перейти в раздел `Настройки -> Настройки продукта -> Почтовые события -> Почтовые шаблоны`;
- в фильтре по "**Тип почтового события**" заполнить "**SAVITSKYI_BXHELPERS_HELPERS_MAIL**" и перейти на страницу настроек почтового шаблона;
- в поле "**Кому**" заполнить необходимые адреса получателей  (для администрации в случае ошибок будет приходить оповещение);

5) В файле `init.php` подключить следующий код:

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

6) Подключить необходимые скрипты и стили в шаблоне:

```php
use SavitskyiHub\BxHelpers\Helpers\Main\Includes;

// Для стилей
Includes::libraryCss();

// Для скриптов
Includes::libraryJs();
```

> **Примечание:** разместить подключение после плагинов и перед подключением скриптов проекта.

7) В директорию `ПУТЬ_К_ДИРЕКТОРИИ_ШаБЛОНА/img/` загрузить необходимые изображения (**главное чтобы они были**):

- no-avatar.png;
- no-image.png;

## Проверка работы

Проверить отправку писем и логов (рассчитано что на сервере настроено почту), для этого необходимо произвести ошибку:

- в командной строке запустить выполнение следующего кода:

```php
use SavitskyiHub\BxHelpers\Helpers\Main\User;

$testDebug = User::getInstance();
$testDebug->$ID;
```

- в результате на почту должно прийти оповещение об ошибке;
- в файле `/local/logs/helpers-debug.log` посмотреть чтобы была перехвачена ошибка;

## Удаление

1) Перейти на страницу командной строки в административной панели и запустить процесс деинсталяции зависимостей:

```php
use SavitskyiHub\BxHelpers\Helpers\Install\Mail_Uninstall_Highload;
use SavitskyiHub\BxHelpers\Helpers\Install\User_Group_Uninstall;

new Mail_Uninstall_Highload("УКАЗАТЬ_ПРЕФИКС");
new User_Group_Uninstall();
```

2) В файле `init.php` удалить код подключения что указан в установке;

3) Отключить подключения стилей и скриптов что указаны в установке;
