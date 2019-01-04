# bx-helpers
Вспомогательная библиотека для удобной разработки и поддержки проекта на платформе 1С-Bitrix

## Установка

1) Компосер ...

2) Перейти на страницу командной строки в административной панели и запустить процесс инсталяции необходимых зависимостей:
```php
use SavitskyiHub\BxHelpers\Helpers\Install\Mail_Install_Highload;
use SavitskyiHub\BxHelpers\Helpers\Install\User_Group_Install;

new Mail_Install_Highload("УКАЗАТЬ_ПРЕФИКС");
new User_Group_Install();
```
3) Скинуть полностю кеш:
- в разделе `Languages->PHP` выбрать "Все" и нажать ""

4) Для почтовых событий установить получателей и т.д.

5) Проверить оправку писем и логов

6) В init.php подключить скедующий код:

7) Подключить стили и скрипты

8) картинкы


## Удаление

1) Выполнить команды

 use SavitskyiHub\BxHelpers\Helpers\Install\Mail_Uninstall_Highload;
   new Mail_Uninstall_Highload("savitskyi");
   
    use SavitskyiHub\BxHelpers\Helpers\Install\User_Group_Uninstall;
   new User_Group_Uninstall();
   
2)   В init.php удалить код подвключения что указан в установке:

3) Отключить подключения стилей и скриптов у себя в шаблоне
