<!DOCTYPE html>

<?php
	require(__DIR__ . "/vendor/autoload.php");
	require(__DIR__ . "/config.php");

	use YoutubeDl\Options;
	use YoutubeDl\YoutubeDl;
	use Symfony\Component\Process\ExecutableFinder;

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

		if (DONT_KEEP_FILES)
		{
			// Delete all files in the output folder before downloading a new video.
			$files = glob(OUTPUT_FOLDER . "/*");

			foreach ($files as $file)
			{
				if (is_file($file))
				{
					unlink($file);
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
					->output(OUTPUT_FORMAT)
					->sourceAddress($_SERVER["SERVER_ADDR"])
					->noPlaylist(true)
					->maxFileSize(MAX_FILE_SIZE)
					->keepVideo($extract_audio)
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

		<!-- CSS stylesheet -->
		<link rel="stylesheet" href="styles/styles.css" />
	</head>
	<body onload="document.querySelector('a[download]')?.click()">
		<!-- Animated GitHub repository icon -->
		<!-- Source: https://tholman.com/github-corners/ -->
		<a href="https://github.com/FlorianLeChat/YouTube-Downloader" target="_blank">
			<svg width="80" height="80" viewBox="0 0 250 250" style="fill: #151513; color: #fff; position: absolute; top: 0; border: 0; right: 0;">
				<path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path>
				<path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;"></path>
				<path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor"></path>
			</svg>
		</a>

		<!-- Title -->
		<h1><a href="https://github.com/FlorianLeChat/YouTube-Downloader" target="_blank">📺</a> YouTube Downloader</h1>

		<!-- Submission form -->
		<p>
			You can download videos (<code>WEBM</code> format) or extract music from them (<code>MP3</code> format). <strong>Only videos from YouTube are supported.</strong><br />
			The conversion time by the remote server may differ depending on the video duration and the requested download type.<br />
			Depending on the file size (<strong><?= MAX_FILE_SIZE; ?>B max</strong>) and the speed of your network connection, the download may take several minutes.
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
			📥 <a href="<?= $download_path ?>" download>Download doesn't start by itself? Please click here</a>.
		<?php endif; ?>

		<!-- Error output -->
		<?php if (!empty($video_output)):  ?>
			<h3>⚠️ Error output ⚠️</h3>

			<p><?= $video_output ?></p>
		<?php endif; ?>
	</body>
</html>