<?php require_once APPPATH . 'views/layouts/header.php'; ?>



<? if ($error): ?> <tt><?= $error ?></tt> <? endif ?>
<form action="create" method="POST" enctype="multipart/form-data">

	<h4>Add New Theme</h4>
	
	Name: <input type="text" size="40" name="name" />
	<br /><br /><br />

	URL: <input type="text" size="30" name="url" /> or Upload: <input type="file" name="userfile" size="20" />
	<br /><br />
	<input type="submit" />
</form>



<?php require_once APPPATH . 'views/layouts/footer.php'; ?>
