<?php
error_reporting(0);
$Config = array(
	"mysql_host" => "localhost",
	"mysql_user" => "root",
	"mysql_pass" => "romanvip",
	"mysql_db" => "pw",
	
	"GiveGold" => true, // ������ ����� ����� ����������� true - ��������, false - ���������
	"GoldCount" => 1000, // ���-�� ����� ������� ����� ������ ��� ����������� (���� GiveGold = true)
	
	"RequireEmailConfirmation" => false, // �������� ������������� �� ����������� �����
	
	"LoginMinLeng" => 3, // ����������� ������ ������
	"LoginMaxLeng" => 15, // ������������ ������ ������
		
	"PassMinLeng" => 5, // ����������� ������ ������
	"PassMaxLeng" => 30, // ������������ ������ ������
	
	"EmailMinLeng" => 10, // ����������� ������ email
	"EmailMaxLeng" => 30, // ������������ ������ email

	"PasswordHash" => 1, // ��� ���� ������ 0 - 1.2.6+, 1 - 1.4.4+
	
	
	//��������� ���� �� ������ ������� ������������� �� ����������� �����
	//������ ������ ������ ������������ �������� ����� ����� smtp �������
	//������ ������ ��� mail.ru, ��� ������ �������� �����, ���� � ��� ���������� ����� ����� � �����
	
	"SMTP_HOST" => "smtp.mail.ru", // SMTP Host
	"SMTP_PORT" => 25, // SMTP Port
	"SMTP_LOGIN" => "test@mail.ru", // SMTP Login
	"SMTP_PASS" => "������ �������", // SMTP Password
	"SMTP_HASH" => "tls", // SMTP login hash
	"SMTP_FROM" => "Pro_PW Support", // �� ���� ���������
	"ServerName" => "Pro_PW", // �������� ������� (������������ � ����������)
	"RegUrl" => "http://192.168.146.130/register/" // ������ �� ����� � ������� ����������� (������������ � ����������) ����� ������� � ����
);
//localhost
//root
//romanvip
//pw

?>
