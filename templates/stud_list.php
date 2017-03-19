<?php
	if (!isset($urgentMessage)
	     ||
	    !isset($tbodyContent)
	     ||
	    !isset($pageCount)
	) throw new \Shinoa\StudentsList\Exceptions\ViewException('One or more variables not set');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<link rel="stylesheet" type="text/css" href="stylesheet.css"/>
	<title>Список абитуриентов</title>
	<meta charset="utf-8">
</head>
<body>
	<div class="search_div">
		<form action="" method="get">
			<label for="search_field">Поиск: </label>
			<input type="search" name="search_field" id="search_field" maxlength="100">
			<div>
				<select name="sort_by">
					<option value="name">Имя</option>
					<option value="surname">Фамилия</option>
				</select>

				<label for="asc">По возр.</label>
				<input type="radio" name="sort_dir" id="asc" value="asc">
				<label for="desc">По убыв.</label>
				<input type="radio" name="sort_dir" id="desc" value="desc">

				<input type="submit" value="Искать">
			</div>
		</form>
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
						<th>Э-почта</th>
						<th>Сумма баллов</th>
						<th>Год рожд.</th>
						<th>Местонахождение</th>
					</tr>
				</thead>
				<tbody>
					<?= $tbodyContent; ?>
				</tbody>
			</table>
		</div>
		<div class="pagination">
			<a href="#">&laquo;</a>
			<?php for ($i = 1; $i <= $pageCount; $i++): ?>
				<a href="#">$i</a>
			<?php endfor; ?>
			<a href="#">&raquo;</a>
		</div>

	<?php else: ?>
		<div class="message">
			<?php echo $urgentMessage; ?>
		</div>
	<?php endif; ?>
</body>