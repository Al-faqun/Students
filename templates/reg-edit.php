<?php
//if (!isset($caption)
//) throw new \Shinoa\StudentsList\Exceptions\ViewException('One or more variables not set');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<link rel="stylesheet" type="text/css" href="/Students/templates/reg-edit.css"/>
	<title>Список абитуриентов</title>
	<meta charset="utf-8">
</head>
<body>
	
	<div class="fields">
		<h1> Регистрация </h1>
		<form action="" method="get">
			<label for="name">Имя: </label>
			<input type="text" name="name" size="40" maxlength="100">
			
			<label for="name">Фамилия: </label>
			<input type="surname" name="surname" size="40" maxlength="100">
			
			<label for="sex">Пол: </label>
			<select name="sex">
				<option value="masculine">Мужской</option>
				<option value="feminine">Женский</option>
			</select>
			
			<label for="group_num">Номер группы: </label>
			<input type="text" name="group_num" size="40" maxlength="50">
			
			<label for="email">Почта: </label>
			<input type="email" name="email" size="40" maxlength="100">
			
			<label for="ege_sum">Сумма баллов ЕГЭ: </label>
			<input type="number" name="ege_sum" maxlength="10">
			
			<label for="birth_year">Год рождения: </label>
			<input type="number" name="birth_year" maxlength="10">
			
			<label for="location">Происхождение: </label>
			<select name="location">
				<option value="local">Местный</option>
				<option value="non_local">Иногородний</option>
			</select>
	
			<input type="submit" value="Отправить">
		</form>
	
	</div>

</body>

