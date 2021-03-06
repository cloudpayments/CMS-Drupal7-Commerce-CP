# CloudPayments модуль для Drupal7 - Commerce
Модуль позволит с легкостью добавить на ваш сайт оплату банковскими картами через платежный сервис CloudPayments. 
Для корректной работы модуля необходима регистрация в сервисе.

Порядок регистрации описан в [документации CloudPayments](https://cloudpayments.ru/Docs/Connect)

### Совместимость:
* Drupal v.7.x;
* Commerce v.1.x;

### Возможности:  
• Одностадийная схема оплаты;  
• Двухстадийная схема оплаты;  
• Отмена, подтверждение и возврат платежей из ЛК CMS;  
• Выбора дизайна виджета;  
• Поддержка онлайн-касс (ФЗ-54);  
• Отправка чеков по email;  
• Отправка чеков по SMS;  
• Отдельный НДС для службы доставки;  

### Установка

Для установки данного модуля необходимо распаковать содержимое архива в папку `/sites/all/modules` вашего сайта и активировать модули из административной панели.
Примечание. Для перевода интерфейса модуля на русский язык необходимо активировать модуль `Locale`, и импортировать файл `cloudpayments.ru.po` через меню `Конфигурация`-`Перевод интерфейса`- вкладка `Импорт`.

### Настройка модуля
Далее заполните и сохраните настройки на странице /admin/config/system/cloudpayments.

![1](img/1.png)
![2](img/2.png)

Описание настроек:
* **Public_id** — Public id сайта из личного кабинета CloudPayments;
* **Password for API** — API Secret из личного кабинета CloudPayments;
* **Схемы проведения платежа** — выбор одностадийной или двухстадийной схемы;
* **Отправка чеков** — Включение/отключение формирования онлайн-чека при оплате;
* **Система налогооблажения** — Тип системы налогообложения;
* **Ставка НДС для товаров** — Указание ставки НДС товаров;
* **Ставка налога для доставки** — Указание ставки НДС службы доставки;

Для одностадийной схемы оплаты достаточно  выбрать и настроить статусы:
* **Заказ Оплачен** - заказ переходит в этот статус, когда происходит списание енежных средств со счёта покупателя;
* **Статус оформленного (неоплаченного) заказа** - модуль переводит заказ в этот статус, если оплата не совершена;
* **Возврат заказа** - перевод в этот статус выполняет возврат денежных средств на счет покупателя;
Для Двухстадийной схемы оплаты необходимо дополнительно  выбрать следующие статусы:
* **Статус авторизованного платежа (DMS)** - статус при котором банком холдируются средства на счёте покупателя, но требуют вашего подтверждения для списания.
* **Статус подтвержденного платежа (DMS)** - статус для подтверждения (функция работает только для аторизованного платежа)
* **Заказ отменён (DMS)** - статус для отмены авторизованных денежных средств. (функция работает только для аторизованного платежа)
* **Поле для телефона** - Позволяет выполнять отправку чеков по SMS вашим клиентам, если поле с номером телефона добавлено [стандартным способом](https://docs.drupalcommerce.org/commerce1/user-guide/customer-profiles/configuring--creating-customer-profiles);

### Настройка вебхуков:

В [личном кабинете](https://merchant.cloudpayments.ru/Account/Login) CloudPayments в настройках вашего сайта вставьте следующие URL для коректной работы модуля:
* (Check) 		http(s)://yourdomainname.ru/cloudpayments/check
* (Pay) 		http(s)://yourdomainname.ru/cloudpayments/pay
* (Confirm)		http(s)://yourdomainname.ru/cloudpayments/confirm
* (Refund)		http(s)://yourdomainname.ru/cloudpayments/refund
![3](img/3.png)
где yourdomainname.ru - доменное имя вашего сайта.  

#### Changelog

= 1.1 = 
* Добавление выбора дизайна виджета;  
* Правка значений ставок НДС;

= 1.0 =
* Публикация модуля.