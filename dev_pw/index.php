<?php
session_start();
Include "config.php";
Include "core/core.php";
$err_str = "";
//echo "<script>alert('message');location.href='';</script>";
if(isset($_POST['capcha'])) {
	if($_POST['capcha'] != $_SESSION['rand_code']) {
		echo "<script>alert('Капча введена не верно!');location.href='';</script>";
	} else {
		$Reg = new Registration($Config);
		$login = strtolower($_POST['login']);
		$status = $Reg->CheckAccountField($login, $_POST['email'], $_POST['pass'], $_POST['repass']);
		switch($status) {
			case 0:
				$err_str = "Поля заполнены не верно!";
				break;
			case 1:
				$err_str = "Логин должен содержать от {$Config['LoginMinLeng']} до {$Config['LoginMaxLeng']} символов!";
				break;
			case 2:
				$err_str = "Пароль должен содержать от {$Config['PassMinLeng']} до {$Config['PassMaxLeng']} символов!";
				break;
			case 3:
				$err_str = "Пароли не совпадают!";
				break;
			case 4:
				$err_str = "Email должен содержать от {$Config['EmailMinLeng']} до {$Config['EmailMaxLeng']} символов!";
				break;
			case 5:
				$err_str = "Такой логин уже есть в базе!";
				break;
			case 6:
				$err_str = "Этот Email уже есть в базе!";
				break;
			case 7:
				$Reg->AddAccount($login, $_POST['email'], $_POST['pass']);
				if ($Config['RequireEmailConfirmation']) {
					$err_str = "Ваш аккаунт ожидает подтверждения по электронной почте - {$_POST['email']}";
				} else {
					$err_str = "Ваш аккаунт успешно зарегистрирован, приятной игры!";
				}
				break;
			}
		}
}

if (isset($_POST['res_capcha'])) {
	if($_POST['res_capcha'] != $_SESSION['rand_code']) {
		echo "<script>alert('Капча введена не верно!');location.href='';</script>";
	} else {
		$Reg = new Registration($Config);
		$status = $Reg->RestorePass_Add($_POST['email']);
		switch($status) {
			case 0:
				$err_str = "Этот Email не найден!";
				break;
			case 1:
				$err_str = "Инструкция была выслана вам на Email!";
				break;
		}
	}
}
if (isset($_POST['set_capcha'])) {
	if($_POST['set_capcha'] != $_SESSION['rand_code']) {
		echo "<script>alert('Капча введена не верно!');location.href='';</script>";
	} else {
		$Reg = new Registration($Config);
		$status = $Reg->RestorePass($_POST['key'], $_POST['pass'],  $_POST['repass']);
		switch($status) {
			case 0:
				$err_str = "Данно ключа восстановления не существует";
				break;
			case 1:
				$err_str = "Пароли не совпадают!";
				break;
			case 2:
				$err_str = "Пароль успешно изменен!";
				break;
			case 3:
				$err_str = "Пароль должен содержать от {$Config['PassMinLeng']} до {$Config['PassMaxLeng']} символов!";
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>PW Registration</title>
	<link rel="stylesheet" href="style/style.css">
</head>
<body>
<?php
if (isset($_GET['mod']) && $_GET['mod'] == "restore") {
echo <<<HTML
<form id="login1" method="POST" action="index.php">
    <h1>Восстановление пароля</h1>
    <fieldset id="inputs">
        <input name="email" type="text" placeholder="E-Mail" required>
        <input name="res_capcha" type="text" placeholder="Введте текст с картинки" required>
        <center><img src="captcha.php"></center>
    </fieldset>
    <fieldset id="actions">
        <input type="submit" id="submit" value="ВОССТАНОВИТЬ">
        <a href="index.php">Регистрация</a>
    </fieldset>
HTML;
} elseif (isset($_GET['key']) && !empty($_GET['key'])) {
	$Reg = new Registration($Config);
	$key = $Reg->ActiveAccount($_GET['key']);
	if($key == true) {
		$key_str = "Ваш аккаунт успешно активирован!";
	} else {
		$key_str = "Такой аккаунт в базе не найден";
	}
echo <<<HTML
<form id="login1">
    <h1>{$key_str}</h1>
HTML;
} elseif (isset($_GET['mod']) && $_GET['mod'] == "newpass") {
$key = $_GET['rkey'];
echo <<<HTML
<form id='login2' method='POST' action='index.php'>
    <h1>Восстановление пароля</h1>
    <fieldset id='inputs'>
        <input name='pass' type='password' placeholder='Пароль' required>
        <input name='repass' type='password' placeholder='Повтор пароля' required>
        <input name='key' type='text' placeholder='Ключ для восстановления' value='{$key}' required>
        <input name='set_capcha' type='text' placeholder='Введте текст с картинки' required>
        <center><img src='captcha.php'></center>
    </fieldset>
    <fieldset id='actions'>
        <input type='submit' id='submit' value='ПОМЕНЯТЬ' name='change_pass'>
        <a href='index.php'>Регистрация</a>
    </fieldset>
HTML;
} else {
echo <<<HTML
<form id='login' method='POST' action='index.php'>
    <h1>Регистрация</h1>
    <fieldset id='inputs'>
        <input name='login' type='text' placeholder='Логин' autofocus required>   
        <input name='email' type='text' placeholder='E-Mail' required>
        <input name='pass' type='password' placeholder='Пароль' required>
        <input name='repass' type='password' placeholder='Повтор пароля' required>
        <input name='capcha' type='text' placeholder='Введте текст с картинки' required>
        <center><img src='captcha.php'></center>
    </fieldset>
    <fieldset id='actions'>
        <input type='submit' id='submit' value='РЕГИСТРАЦИЯ'>
        <a href='index.php?mod=restore'>Забыли пароль?</a>
    </fieldset>
HTML;
}
?>
	<fieldset id='actions'>
		<font color="red" size="2"><center><?php echo $err_str; ?></center></font><br/>
		<a href="http://romanvip.ru/index.php"><font color="gray" size="0.1">RomanVip.Ru Team</font></a>
	</fieldset>
</form>
</body>
</html>