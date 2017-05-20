<?php
	if (!isset($urgentMessage)
	     ||
	    !isset($tbodyContent)
	     ||
	    !isset($pageCount)
		 ||
		!isset($appStatusText)
	) throw new \Shinoa\StudentsList\Exceptions\ViewException('One or more variables not set');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<link rel="stylesheet" type="text/css" href="/Students/templates/list.css"/>
	<title>Список абитуриентов</title>
	<meta charset="utf-8">
</head>
<body>
	<div class="left ">
		<a href="reg-edit.php">Добавить сведения о себе</a>
	</div>
	<div class="search_div">
		<form action="" method="get">
			<label for="search_text">Поиск: </label>
			<input type="search" name="search_text" id="search_text"  maxlength="100">
			<label for="search_field">в </label>
			<select name="search_field">
				<option value="name">Имя</option>
				<option value="surname">Фамилия</option>
				<option value="sex">Пол</option>
				<option value="group_num">Номер группы</option>
				<option value="email">Почта</option>
				<option value="ege_sum">Сумма баллов ЕГЭ</option>
				<option value="birth_year">Год рождения</option>
				<option value="location">Происхождение</option>
			</select>
			<div>
				<label for="sort_by">Сортировать: </label>
				<select name="sort_by">
					<option value="name">Имя</option>
					<option value="surname">Фамилия</option>
					<option value="sex">Пол</option>
					<option value="group_num">Номер группы</option>
					<option value="email">Почта</option>
					<option value="ege_sum">Сумма баллов ЕГЭ</option>
					<option value="birth_year">Год рождения</option>
					<option value="location">Происхождение</option>
				</select>

				<label for="asc">По возр.</label>
				<input type="radio" name="order" id="asc" value="asc" checked="checked">
				<label for="desc">По убыв.</label>
				<input type="radio" name="order" id="desc" value="desc">

				<input type="submit" value="Искать">
			</div>
		</form>
		<div class="appstatchoice">
			<p>Режим: <?=$appStatusText?> </p>
			<form action="" method="post">
				<label for="appStatus">Изменить режим работы приложения:</label>
				<select name="appStatus">
					<option value="0">'In development'</option>
					<option value="1" selected>'In production'</option>
				</select>
				<input type="submit" value="Изменить">
			</form>
			<form action="" method="post">
				<input type="hidden" name="evokeException">
				<input type="submit" value="Вызвать исключение">
			</form>
			<form action="" method="post">
				<input type="hidden" name="evokeError">
				<input type="submit" value="Вызвать ошибку">
			</form>
			<a href="/Students/errors.log">Просмотреть лог</a>
		</div>
		
		
		
	</div>
	
	<?php if (empty($urgentMessage)) : ?>
		<div class="list_stud">
			<table>
				<caption>Список абитуриентов</caption>
				<thead>
					<tr>
						<th>Имя</th>
						<th>Фамилия</th>
						<th>Пол</th>
						<th>Номер группы</th>
						<th>Почта</th>
						<th>Сумма баллов</th>
						<th>Год рожд.</th>
						<th>Происхождение</th>
					</tr>
				</thead>
				<tbody>
					<?= $tbodyContent; ?>
				</tbody>
			</table>
			<div class="pagination">
				<form action="" method="get">
					<?php for ($i = 1; $i <= $pageCount; $i++): ?>
						<a href="?<?=$queries[$i]?>" class="inline-block"> <?=$i?> </a>
					<?php endfor; ?>
				</form>
			</div>
		</div>
		

	<?php else: ?>
		<div class="message">
			<?=$urgentMessage ?>
		</div>
	<?php endif; ?>
</body>