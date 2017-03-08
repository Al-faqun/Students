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
		<title>Student's book project: Oops, something went wrong</title>
		<meta http-equiv="content-type"
		content="text/html; charset=utf-8"/>
	</head>
	<body>
		<p><?php echo $error; ?></p>
		<form action="<?php echo $form_action ?>" method="<?php echo $form_method ?>">
			<input type="submit" value="<?php echo $input_value ?>"/>
		</form>
	</body>
</html>