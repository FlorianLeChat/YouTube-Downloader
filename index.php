<!DOCTYPE html>

<?php
	require(__DIR__ . "/vendor/autoload.php");

	use YoutubeDl\Options;
	use YoutubeDl\YoutubeDl;
	use Symfony\Component\Process\ExecutableFinder;

	const OUTPUT_FOLDER = "output";

	// Retrieve the URL and identifier of the video.
	$url = $_POST["url"] ?? "";
	$audio = boolval($_POST["audio"] ?? "");
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
		// Downloading through YouTube-DL.
		$file = OUTPUT_FOLDER . "/$identifier." . ($audio ? "mp3" : "webm");

		if (!file_exists(OUTPUT_FOLDER))
		{
			mkdir(OUTPUT_FOLDER);
		}

		if (!file_exists($file))
		{
			$finder = new ExecutableFinder();
			$executable = $finder->find("youtube-dl", null, ["/usr/local/bin"]) ?? $finder->find("yt-dlp", null, ["/usr/local/bin"]);

			$yt = new YoutubeDl();
			$yt->setBinPath($executable ?? "/usr/local/bin/youtube-dl");

			$collection = $yt->download(
				Options::create()
					->output("%(id)s.%(ext)s")
					->sourceAddress($_SERVER["SERVER_ADDR"])
					->noPlaylist(true)
					->extractAudio($audio)
					->audioFormat($audio ? "mp3" : null)
					->audioQuality("0")
					->downloadPath(OUTPUT_FOLDER)
					->url("https://www.youtube.com/watch?v=$identifier")
			);

			foreach ($collection->getVideos() as $video)
			{
				if ($video->getError() !== null)
				{
					$output = $video->getError();
					break;
				}
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
				/* Input field */
				width: calc(100% - 0.5rem);
				display: block;
				max-width: 20rem;
				margin-bottom: 1rem;
			}

			input[type = submit]
			{
				/* Submit button */
				display: block;
				margin-top: 1rem;
			}
		</style>
	</head>
	<body onload="document.querySelector('a[download]')?.click()">
		<!-- Title -->
		<h1><a href="https://github.com/FlorianLeChat/YouTube-Downloader" target="_blank">📺</a> YouTube Downloader</h1>

		<!-- Submission form -->
		<p>
			You can download videos (WEBM format) or extract music from them (MP3 format). <strong>Only videos from YouTube are officially supported.</strong><br />
			The conversion time by the remote server may differ depending on the video duration and the requested download type.<br />
			Depending on the file size and the speed of your network connection, the download may take several minutes.
		</p>

		<form method="POST">
			<label for="url">URL to the YouTube video:</label>
			<input type="text" autoComplete="off" spellCheck="false" id="url" name="url" placeholder="https://www.youtube.com/watch?v=..." required />

			<label for="audio">Audio only?</label>
			<input type="checkbox" id="audio" name="audio" />

			<input type="submit" value="Download" />
		</form>

		<!-- Download link -->
		<?php if (!empty($file)):  ?>
			<a href="<?= $file ?>" download></a>
		<?php endif; ?>

		<!-- Error output -->
		<?php if (!empty($output)):  ?>
			<h3>⚠️ Error output ⚠️</h3>

			<p><?= $output ?></p>
		<?php endif; ?>
	</body>
</html>