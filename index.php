<!DOCTYPE html>

<?php
	require(__DIR__ . "/vendor/autoload.php");

	use YoutubeDl\Options;
	use YoutubeDl\YoutubeDl;
	use Symfony\Component\Process\ExecutableFinder;

	const OUTPUT_FOLDER = "output";

	// Retrieve the URL and identifier of the video.
	$video_id = "";
	$video_url = $_POST["url"] ?? "";
	$extract_audio = boolval($_POST["audio"] ?? "");

	if (!empty($video_url))
	{
		// Looking for the unique identifier in the URL.
		// Source: https://gist.github.com/ghalusa/6c7f3a00fd2383e5ef33
		$matches = [];

		preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $matches);

		if (count($matches) > 0)
		{
			// Return of the first result only.
			$video_id = $matches[1];
		}
		else
		{
			$video_output = "Incomplete or invalid YouTube video URL.";
		}
	}

	if (!empty($video_id))
	{
		if (!file_exists(OUTPUT_FOLDER))
		{
			// Create the output folder if it does not exist.
			mkdir(OUTPUT_FOLDER);
		}
		{

			{
				{
				}
			}
		}

		// Checks if a file is already saved or if the video needs to be downloaded.
		$download_path = "";

		if (!file_exists($download_path))
		{
			$downloader_path = new ExecutableFinder();
			$executable_path = $downloader_path->find("youtube-dl", null, ["/usr/local/bin"]) ?? $downloader_path->find("yt-dlp", null, ["/usr/local/bin"]);

			$youtube_downloader = new YoutubeDl();
			$youtube_downloader->setBinPath($executable_path ?? "/usr/local/bin/youtube-dl");

			$download_stack = $youtube_downloader->download(
				Options::create()
					->output("%(title)s-%(id)s.%(ext)s")
					->sourceAddress($_SERVER["SERVER_ADDR"])
					->noPlaylist(true)
					->extractAudio($extract_audio)
					->audioFormat($extract_audio ? "mp3" : null)
					->audioQuality("0")
					->downloadPath(OUTPUT_FOLDER)
					->url("https://www.youtube.com/watch?v=$video_id")
			);

			foreach ($download_stack->getVideos() as $video)
			{
				if ($video->getError() !== null)
				{
					// Error while downloading/converting.
					$video_output = $video->getError();
				}
				else
				{
					// Save the downloaded file.
					$download_path = $video->getFilename();
				}
			}
		}
	}
?>

<html lang="en" dir="auto">
	<head>
		<!-- Document metadata -->
		<meta charset="utf-8" />
		<meta name="author" content="Florian Trayon" />
		<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />

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
		<?php if (!empty($download_path)):  ?>
			<a href="<?= $download_path ?>" download></a>
		<?php endif; ?>

		<!-- Error output -->
		<?php if (!empty($video_output)):  ?>
			<h3>⚠️ Error output ⚠️</h3>

			<p><?= $video_output ?></p>
		<?php endif; ?>
	</body>
</html>