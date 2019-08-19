<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Remove -min from images</title>
	<meta name="description" content="">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="icon" href="assets/favicon.ico">
	<link rel="stylesheet" href="assets/main.css">
</head>

<body>

	<header>
		<div class="box">
			<a href="#" class="logo">Rename</a>
			<div class="text">Rename images easily! ;)</div>
		</div>
	</header>

	<main>
		<section class="inner">
			<div class="box">
				<h1>Remove -min from images.</h1>

				<div class="upload">
					<a href="http://terms.fox-m.com/rename/" class="refresh" title="Reload page"></a>

					<form action="" method="post" id="myForm" enctype="multipart/form-data">
						<input id='upload' name="upload[]" type="file" multiple="multiple" data-multiple-caption="{count} files selected" accept=".zip" />
						<label for="upload"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg><span>Choose a zip-file(s)</span></label>
						<input type="submit" name="submit" value="Submit">
					</form>

					<?php
						if(isset($_POST['submit'])){
							if(count($_FILES['upload']['name']) > 0){
								for($i=0; $i<count($_FILES['upload']['name']); $i++) {
									$tmpFilePath = $_FILES['upload']['tmp_name'][$i];

									if($tmpFilePath != "") {
										$shortname = $_FILES['upload']['name'][$i];
										$filePath = "docs/" . $_FILES['upload']['name'][$i];

										if(move_uploaded_file($tmpFilePath, $filePath)) {
											$files[] = $shortname;
										}
									}
								}
							}
							$path = "images/" . $ID;
							$documents = scandir("docs" . '/' . $ID );

							foreach ($documents as $athely){
								if($athely=="." || $athely=="..") continue;

								$target_path = "docs/".$ID."/".$athely;
								$file = $athely;

								$zip = new ZipArchive;
								$res = $zip->open($target_path);

								if ($res === TRUE) {
									$zip->extractTo($path);
									$zip->close();
								} else {
									echo "<div class='result'><h3>Doh! I couldn't open $athely . </h3></div>";
								}
							}

							echo "<div class='result'><h2>Uploaded:</h2>";
							if(is_array($files)){echo "<ul>";foreach($files as $file){echo "<li>$file</li>";}echo "</ul>";}
							$path = 'images/';
							if ($handle = opendir($path)) {
								while (false !== ($file = readdir($handle))) {
									if(is_file($path.$file)) {
										$new_name = str_replace(['-min','file'],'', $file);
										rename($path.$file, $path.$new_name);
									}
								}
								closedir($handle);
							}

							$rootPath = realpath('images');
							$zip = new ZipArchive();
							$zip->open('images.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
							$filesToDelete = array();
							$files = new RecursiveIteratorIterator(
								new RecursiveDirectoryIterator($rootPath),
								RecursiveIteratorIterator::LEAVES_ONLY
							);
							foreach ($files as $name => $file) {
								if (!$file->isDir()) {
									$filePath = $file->getRealPath();
									$relativePath = substr($filePath, strlen($rootPath) + 1);
									$zip->addFile($filePath, $relativePath);
									if ($file->getFilename() != 'important.txt'){
										$filesToDelete[] = $filePath;
									}
								}
							}
							$zip->close();

							foreach ($filesToDelete as $file) {unlink($file);}

							$docs = glob('docs/*');
							foreach($docs as $zip){if(is_file($zip)) {unlink($zip);}}

							echo '<div class="dozip"><a href="images.zip" download="download">Download zip</a><a class="red" href="?delete=1">Delete ZIP!</a></div></div></div>';
						}

						$delete = $_GET['delete'];
						if ($delete == 1) {
							unlink('images.zip');
						}
					?>
				</div>
			</div>
		</section>
	</main>

	<footer>
		<div class="box">
			<strong>© koliesnyk</strong>
			<p>All rights reserved.</p>
		</div>
	</footer>

	<script src="assets/jquery.js"></script>

	<script>
		$(document).ready(function($) {
			var input = $('#upload'),
			label   = input.next('label'),
			labelVal = label.html();

			input.on('change', function( e ) {
				var fileName = '';
				if(this.files && this.files.length > 1)
					fileName = (this.getAttribute( 'data-multiple-caption' ) || '').replace( '{count}', this.files.length );
				else if( e.target.value )
					fileName = e.target.value.split( '\\' ).pop();
				if(fileName)
					$(label).find('span').html(fileName);
				else
					$(label).html(labelVal);
			});
		});
	</script>

</body>
</html>