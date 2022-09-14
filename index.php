<?php
	//
	// Convertisseur de vidéos YouTube sous format MP3.
	// Source : https://github.com/norkunas/youtube-dl-php
	//

	declare(strict_types = 1);
	require(__DIR__ . "/vendor/autoload.php");

	use YoutubeDl\Options;
	use YoutubeDl\YoutubeDl;

	// Informations de débogage.
	ini_set("display_errors", true);
	ini_set("display_startup_errors", true);

	error_reporting(E_ALL);

	// Fonction pour envoyer un téléchargement à l'utilisateur.
	function sendDownload(string $file): void
	{
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
		header("Expires: 0");
		header("Cache-Control: must-revalidate");
		header("Pragma: public");
		header("Content-Length: " . filesize($file));

		readfile($file);
	}

	// On vérifie d'abord si une adresse URL a été renseignée ou non.
	$url = $_GET["url"] ?? "";
	$identifier = "";

	if (!empty($url))
	{
		// On effectue une recherche dans l'URL pour chercher l'identifiant
		//	unique de la vidéo.
		$matches = [];

		preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $matches);

		if (count($matches) > 0)
		{
			// Si la recherche semble correcte, on récupère le premier résultat.
			$identifier = $matches[1];
		}
	}

	if (!empty($identifier))
	{
		// On vérifie si une copie en cache est déjà présente.
		$file = "output/$identifier.mp3";

		if (file_exists($file))
		{
			sendDownload($file);
			exit();
		}

		// Si ce n'est pas le cas, on lance le téléchargement.
		$yt = new YoutubeDl();
		$yt->setBinPath("/usr/local/bin/yt-dlp");

		$collection = $yt->download(
			Options::create()
				->output("%(id)s.%(ext)s")
				->sourceAddress($_SERVER["SERVER_ADDR"]) // https://github.com/ytdl-org/youtube-dl#http-error-429-too-many-requests-or-402-payment-required
				->noPlaylist(true)
				->extractAudio(true)
				->audioFormat("mp3")
				->audioQuality("0")
				->downloadPath("output")
				->url("https://www.youtube.com/watch?v=$identifier")
		);

		// Une fois le téléchargement, on itére alors à travers toutes les
		//	vidéos et musiques téléchargées.
		foreach ($collection->getVideos() as $video)
		{
			if ($video->getError() !== null)
			{
				// Si une erreur survient, on l'indique à l'utilisateur.
				echo("Erreur de téléchargement : {$video->getError()}.");
			}
			else
			{
				// Sinon, on lance enfin le téléchargement du fichier.
				sendDownload($file);
			}
		}
	}
?>

<html lang="en">
	<head>
		<!-- Document metadata -->
		<meta charset="utf-8" />
		<meta name="author" content="Florian Trayon" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!-- Document title -->
		<title>YouTube Downloader</title>

		<!-- Document icons -->
		<link rel="icon" type="image/webp" sizes="16x16" href="assets/favicons/16x16.webp" />
		<link rel="icon" type="image/webp" sizes="32x32" href="assets/favicons/32x32.webp" />
		<link rel="icon" type="image/webp" sizes="48x48" href="assets/favicons/48x48.webp" />
		<link rel="icon" type="image/webp" sizes="192x192" href="assets/favicons/192x192.webp" />
		<link rel="icon" type="image/webp" sizes="512x512" href="assets/favicons/512x512.webp" />
		<link rel="apple-touch-icon" href="assets/favicons/180x180.webp" />

		<!-- CSS style rules -->
		<style>
			input[type = text]
			{
				/* Home page input field */
				width: calc(100% - 0.5rem);
				display: block;
				max-width: 20rem;
				margin-bottom: 1rem;
			}
		</style>
	</head>
	<body>
		<form method="GET">
			<label for="url">Lien vers la musique/vidéo YouTube :</label>
			<input type="text" autoComplete="off" spellCheck="false" id="url" name="url" required />

			<input type="submit" value="Télécharger" />
		</form>
	</body>
</html>