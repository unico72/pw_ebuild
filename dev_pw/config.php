<?php
error_reporting(0);
$Config = array(
	"mysql_host" => "localhost",
	"mysql_user" => "root",
	"mysql_pass" => "romanvip",
	"mysql_db" => "pw",
	
	"GiveGold" => true, // Выдача голда после регистрации true - включено, false - выключено
	"GoldCount" => 1000, // Кол-во голда которые будет выдано при регистрации (если GiveGold = true)
	
	"RequireEmailConfirmation" => false, // Включить подтверждение по электронной почте
	
	"LoginMinLeng" => 3, // Минимальная длинна логина
	"LoginMaxLeng" => 15, // Максимальная длинна логина
		
	"PassMinLeng" => 5, // Минимальная длинна пароля
	"PassMaxLeng" => 30, // Максимальная длинна пароля
	
	"EmailMinLeng" => 10, // Минимальная длинна email
	"EmailMaxLeng" => 30, // Максимальная длинна email

	"PasswordHash" => 1, // Тип хэша пароля 0 - 1.2.6+, 1 - 1.4.4+
	
	
	//Настройки если вы хотите сделать подтверждение по электронной почте
	//Данный скрипт скрипт поддерживает отправку писем через smtp сервера
	//Вписан пример для mail.ru, для других сервисов адрес, порт и тип шифрования можно найти в инете
	
	"SMTP_HOST" => "smtp.mail.ru", // SMTP Host
	"SMTP_PORT" => 25, // SMTP Port
	"SMTP_LOGIN" => "test@mail.ru", // SMTP Login
	"SMTP_PASS" => "пароль отпочты", // SMTP Password
	"SMTP_HASH" => "tls", // SMTP login hash
	"SMTP_FROM" => "Pro_PW Support", // От кого сообщения
	"ServerName" => "Pro_PW", // Название сервера (используется в сообщениях)
	"RegUrl" => "http://192.168.146.130/register/" // Ссылка до папки с файлами регистрации (используется в сообщениях) можно указать и сайт
);
//localhost
//root
//romanvip
//pw

?>
