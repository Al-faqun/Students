<?php
	if (!isset($error)
			OR
		!isset($form_action)  
			OR 
		!isset($form_method) 
			OR 
		!isset($input_value)) 
		throw new Exception('No_var'); 
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Oops, something went wrong</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	</head>
	<body>
		<blockquote>
		<?php if (is_array($error)) :
				foreach ($error as $element) :
					if (is_array($element)) : ?>
					<blockquote>
						<?php foreach ($element as $line) :
							echo $line . '<br>';
							endforeach; ?>
					</blockquote>
					<?php else : echo $element . '<br>';
					endif;
				endforeach; ?>
		<?php else : ?>
			<p> <?= $error ?> </p>
		<?php endif; ?>
		</blockquote>
		<form action="<?=$form_action ?>" method="<?=$form_method ?>">
			<input type="submit" value="<?=$input_value ?>"/>
		</form>
	</body>
</html>