<?php
	if (!isset($url)
			OR
		!isset($text)
    )
		throw new Exception('No_var'); 
?>

<!DOCTYPE html>
<html>
	<head>
        <link rel="stylesheet" type="text/css" href="/css/error.css"/>
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
        <a href="<?=$url ?>" class="button"><?=$text ?></a>
		
	</body>
</html>