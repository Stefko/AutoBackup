<!DOCTYPE html>
<html lang="de">

<head>
	<meta charset="utf-8">
	<title>Auto Backup</title>
	<meta name="author" content="Stefan 'Stefko' Kohler">
	<meta name="copyright" content="(c) 2018 by Lobsterlounge Digital Media Design - https://lobsterlounge.de" />
	<meta name="description" content="Auto Backup - DB und Daten automatisch per CronJob sichern">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<meta name="robots" content="noindex,nofollow">
	<meta http-equiv="expires" content="43200">
	<script>
		document.addEventListener("touchstart", function() {}, !0)
	</script>
	<script language="JavaScript"></script>
	<link href="https://fonts.googleapis.com/css?family=Roboto:300|Roboto+Mono:100,300" rel="stylesheet">
	<style type="text/css">
		/* reset */
		blockquote,body,code,dd,div,dl,dt,fieldset,figure,form,h1,h2,h3,h4,h5,h6,input,legend,li,ol,p,pre,table,td,textarea,th,ul{margin:0;padding:0}table{border-spacing:0;border-collapse:collapse}caption,td,th{text-align:left;text-align:start;vertical-align:top}abbr,acronym{font-variant:normal;border-bottom:1px dotted #666;cursor:help}blockquote,q{quotes:none}fieldset,img{border:0}sup{vertical-align:text-top}sub{vertical-align:text-bottom}del{text-decoration:line-through}ins{text-decoration:none}article,aside,figcaption,figure,footer,header,nav,section{display:block}button,input,select,textarea{font-family:inherit;font-size:99%;font-weight:inherit}code,pre{font-family:Monaco,monospace}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:400}h1{font-size:1.8333em}h2{font-size:1.6667em}h3{font-size:1.5em}h4{font-size:1.3333em}table{font-size:inherit}caption,th{font-weight:700}a{color:#00f}h1,h2,h3,h4,h5,h6{margin-top:1em}blockquote,form,h1,h2,h3,h4,h5,h6,ol,p,pre,table,ul{margin-bottom:12px}
		
		body {
			min-height: 100vh;
			font-family: 'Roboto', sans-serif;
			letter-spacing: 0.1em;
			background-color: #11a9e2;
			font-weight: lighter;
			background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0d86b3), to(#11a9e2));
		}
		
		.wrapper {
			margin: 10px 0 40px 20px;
		}
		
		main {
			min-height: calc(100vh - 60px);
		}
		
		h1 {
			margin: 10px 0 40px 20px;
			text-shadow: 0 1px 0 #ccc, 0 2px 0 #c9c9c9, 0 3px 0 #bbb, 0 4px 0 #b9b9b9, 0 5px 0 #aaa, 0 6px 1px rgba(0, 0, 0, .1), 0 0 5px rgba(0, 0, 0, .1), 0 1px 3px rgba(0, 0, 0, .3), 0 3px 5px rgba(0, 0, 0, .2), 0 5px 10px rgba(0, 0, 0, .25), 0 10px 10px rgba(0, 0, 0, .2), 0 20px 20px rgba(0, 0, 0, .15);
			font: bold 80px/1 "Helvetica Neue", Helvetica, Arial, sans-serif;
			color: #fff;
		}
		
		h2 {
			color: aliceblue;
/*			text-decoration: underline;*/
			width:200px;
			padding:2px 5px;
			border: 2px solid aliceblue;
			border-top:none;
			border-right:none;
		}
		
		p, li {
			color:aliceblue;
			line-height:1.75rem;
		}
		
		li {
			margin-left:20px;
		}
		
		strong {
			font-family: 'Roboto Mono';
			padding:2px 5px;
			border: 2px solid aliceblue;
			background-color: dodgerblue;
		}
		
		a:link,
		a:visited {
			color:aliceblue;
			text-decoration:none;
			border-bottom: 2px solid aliceblue;
				-webkit-transition: all 0.5s ease;
    		transition: all 0.5s ease;
		}
		
		a:hover,
		a:active {
			color:cyan;
			text-decoration:none;
			border-bottom: 2px solid cyan;
				-webkit-transition: all 0.5s ease;
    		transition: all 0.5s ease;
		}
		
		footer {
			width:100%;
			background:rgba(255, 255, 255, 0.5);
			text-align: center;
			font-style: italic;
			height: 30px;
		  	margin-top: -28px;
			padding: 5px 0 0 0;
			box-shadow: 0 -2px 0px rgba(255, 255, 255, 1);
		}
		
	</style>
</head>

<body>
	<main>
		<h1>Auto-Backup</h1>

		<div class="wrapper">
			<?php
			// /* zugehöriger Conjob: */
			// MAILTO=name@domain.tld
			// 1 0 * * * /usr/bin/lynx -dump http://domain.de/backup.php
			
			// Variablen für das Backup
			$dbHost = "localhost";					// Datenbank Host
			$dbDatabase = "database";				// Name der Datenbank
			$dbUser = "user";						// Datenbank User
			$dbPass = "password";					// Datenbank Passwort
			$project = "Projektname";				// Projektname
			$root = "https://domain.tld/";			// Http-Pfad der Installation mit / am Ende
			$path = "Backup-Folder"; 				// Ordner für Sicherung (Ordner muss existieren)
			$prefix = "backup";						// Backup Name (Daten)
			$date = date("Y-m-d_H-i-s");			// Datumsformat (für Filename)
			$days = 14;								// Angabe in Tagen nach denen Sicherungen gelöscht werden sollen
			$fileType = gz;							// Dateiendung welche gelöscht werden soll
			
			
			// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			// Ab hier keine Änderungen mehr nötig
			// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			
			// Datenbank sichern und als gz-Archiv ablegen
			shell_exec('mysqldump -h '.$dbHost.' -u '.$dbUser.' -p'.$dbPass.' '.$dbDatabase.'  | gzip > '.$path.'/'.$date.'_'.$dbDatabase.'.sql.gz');
			
			// Daten sichern und als gz-Archiv ablegen
			shell_exec('tar --exclude=\''.$path.'\'* -cvpzf '.$path.'/'.$date.'_'.$prefix.'.tar.gz ./* .??*');
			
			// Textausgabe
			echo '<p>Die <strong>'.$project.'</strong> Sicherung wurde am <strong>'.$date.'</strong> erstellt.</p>';
			echo '<h2>Download:</h2>';	
			echo '<ul><li><a href='.$path.'/'.$date.'_'.$dbDatabase.'.sql.gz'.'>Datenbank</a></li>';
			echo '<li><a href='.$path.'/'.$date.'_'.$prefix.'.tar.gz'.'>Datenstruktur</a></li></ul>';
		
			
			// Ältere DB-Sicherungen löschen
			function delete_older_than($dir, $max_age) {
  				$list = array();
  				$limit = time() - $max_age;
  				$dir = realpath($dir);
  				if (!is_dir($dir)) {
  				  return;
  				}
  				$dh = opendir($dir);
  				if ($dh === false) {
  				  return;
  				}
  				while (($file = readdir($dh)) !== false) {
  				  $file = $dir . '/' . $file;
  				  if (!is_file($file)) {
  				    continue;
  				  }
  				  if (filemtime($file) < $limit) {
  				    $list[] = $file;
  				    unlink($file);
  				  }
  				}
  				closedir($dh);
  				return $list;
			}
		
			$deleted = delete_older_than($path, 3600*24*$days);
			
			echo '<hr><p>'.count($deleted)." alte Backups gelöscht:<br>" .implode("<br>", $deleted);
			
			?>
			
		</div>
	</main>
	<footer>
		<div>
			<span class="author">Author: <a href="https://github.com/Stefko/AutoBackup" target="_blank">Stefan Kohler</a></span>
		</div>
	</footer>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</body>

</html>