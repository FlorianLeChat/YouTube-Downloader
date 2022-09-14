<!DOCTYPE html>

<?php
	require(__DIR__ . "/vendor/autoload.php");

	use YoutubeDl\Options;
	use YoutubeDl\YoutubeDl;
	use Symfony\Component\Process\ExecutableFinder;

	// Retrieve the URL and identifier of the video.
	$url = $_GET["url"] ?? "";
	$identifier = "";

	if (!empty($url))
	{
		// Looking for the unique identifier in the URL.
		$matches = [];

		preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $matches);

		if (count($matches) > 0)
		{
			// Return of the first result only.
			$identifier = $matches[1];
		}
		else
		{
			$output = "Incomplete or invalid YouTube video URL.";
		}
	}

	if (!empty($identifier))
	{
		// Checking the cache of previously downloaded files.
		$file = "output/$identifier.mp3";

		function sendDownload(string $file): void
		{
			header("Content-Description: File Transfer");
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
			header("Expires: 0");
			header("Cache-Control: must-revalidate");
			header("Pragma: public");
			header("Content-Length: " . filesize($file));

			ob_clean();
			flush();

			readfile($file);
		}

		if (file_exists($file))
		{
			sendDownload($file);
			exit();
		}

		// Downloading through YouTube-DL.
		$finder = new ExecutableFinder();
		$executable = $finder->find("youtube-dl", null, ["/usr/local/bin"]) ?? $finder->find("yt-dlp", null, ["/usr/local/bin"]);

		$yt = new YoutubeDl();
		$yt->setBinPath($executable ?? "/usr/local/bin/youtube-dl");

		if (!file_exists("output"))
		{
			mkdir("output");
		}

		$collection = $yt->download(
			Options::create()
				->output("%(id)s.%(ext)s")
				->sourceAddress($_SERVER["SERVER_ADDR"])
				->noPlaylist(true)
				->extractAudio(true)
				->audioFormat("mp3")
				->audioQuality("0")
				->downloadPath("output")
				->url("https://www.youtube.com/watch?v=$identifier")
		);

		// Sends downloaded files to the user.
		foreach ($collection->getVideos() as $video)
		{
			if ($video->getError() !== null)
			{
				$output = $video->getError();
			}
			else
			{
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
		<!-- Title -->
		<h1><a href="https://github.com/FlorianLeChat/YouTube-Downloader" target="_blank">📺</a> YouTube Downloader</h1>

		<!-- Submission form -->
		<p></p>

		<form method="GET">
			<label for="url">URL to the YouTube video:</label>
			<input type="text" autoComplete="off" spellCheck="false" id="url" name="url" placeholder="https://www.youtube.com/watch?v=..." required />

			<input type="submit" value="Télécharger" />
		</form>

		<!-- Error output -->
		<?php if (!empty($output)):  ?>
			<h3>⚠️ Error output ⚠️</h3>

			<p><?= $output ?></p>
		<?php endif; ?>
	</body>
</html>