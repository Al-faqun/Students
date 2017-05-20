<?php
use Shinoa\StudentsList\RegEditView;
if (!isset($caption) || !isset($submitButName) || !isset($mesagge) || !isset($error)
	 ||
	!isset($defFields['nameVal']) || !isset($defFields['surnameVal'])
	 ||
	!isset($defFields['mascVal']) || !isset($defFields['femVal'])
	 ||
	!isset($defFields['groupVal']) || !isset($defFields['emailVal'])
	 ||
	!isset($defFields['egeSumVal']) || !isset($defFields['birthVal'])
	  ||
	!isset($defFields['localVal']) || !isset($defFields['nonLocalVal'])
) throw new \Shinoa\StudentsList\Exceptions\ViewException('One or more variables not set');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<link rel="stylesheet" type="text/css" href="/Students/templates/reg-edit.css"/>
	<title>Список абитуриентов</title>
	<meta charset="utf-8">
</head>
<body>
	<div class="left ">
		<a href="/Students">Вернуться на страницу поиска</a>
	</div>
	<div class="fields">
		<h1><?=RegEditView::esc($caption) ?></h1>
		
		<?php if ( empty($error) ) : ?>
			<strong><?=$mesagge ?></strong>
		<?php else : ?>
			<span class="error"><strong><?=$error ?></strong></span>
		<?php endif; ?>
		
		<form action="" method="post">
			<div class = "field">
				<label for="name">Имя: </label>
				<input type="text" name="name" size="40" maxlength="100" value="<?=$defFields['nameVal']?>" required>
				<span class="description">Буквы, цифры и знаки подчёркивания, длина до ста символов</span>
			</div>
			
			<div class = "field">
				<label for="surname">Фамилия: </label>
				<input type="surname" name="surname" size="40" maxlength="100" value="<?=$defFields['surnameVal']?>" required>
				<span class="description">Буквы, цифры и знаки подчёркивания, длина до ста символов</span>
			</div>
			
			<div class = "field">
			<label for="sex">Пол: </label>
				<select name="sex" >
					<option value="masculine" <?=$defFields['mascVal']?> >Мужской</option>
					<option value="feminine" <?=$defFields['femVal']?> >Женский</option>
				</select>
			</div>
			
			<div class = "field">
				<label for="group_num">Номер группы: </label>
				<input type="text" name="group_num" size="40" maxlength="5" value="<?=$defFields['groupVal']?>" required>
				<span class="description">Буквы, цифры и знаки подчёркивания, длина до пяти символов</span>
			</div>
			
			<div class = "field">
				<label for="email">Почта: </label>
				<input type="email" name="email" size="40" maxlength="100" value="<?=$defFields['emailVal']?>" required>
				<span class="description">В соответствии с RFC 822, длина до 254 символов</span>
			</div>

			<div class = "field">
				<label for="ege_sum">Сумма баллов ЕГЭ: </label>
				<input type="number" name="ege_sum" maxlength="10" value="<?=$defFields['egeSumVal']?>" required>
				<span class="description">Число от 1 до 500</span>
			</div>

			<div class = "field">
				<label for="birth_year">Год рождения: </label>
				<input type="number" name="birth_year" maxlength="10" value="<?=$defFields['birthVal']?>" required>
				<span class="description">Число от 1900 до 2017</span>
			</div>

			<div class = "field">
				<label for="location">Происхождение: </label>
				<select name="location">
					<option value="local" <?=$defFields['localVal']?> >Местный</option>
					<option value="non_local" <?=$defFields['nonLocalVal']?> >Иногородний</option>
				</select>
			</div>
			
			<input type="submit" value="<?=RegEditView::esc($submitButName)?>" >
			<input type="hidden" name="form_sent" value="1">
		</form>
	
	</div>

</body>
