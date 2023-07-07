<!DOCTYPE html>

<?php
	require(__DIR__ . "/vendor/autoload.php");
	require(__DIR__ . "/config.php");

	use YoutubeDl\Options;
	use YoutubeDl\YoutubeDl;
	use Symfony\Component\Process\ExecutableFinder;

	// Retrieve the URL and identifier of the video.
	$videoId = "";
	$videoUrl = $_POST["url"] ?? "";
	$cloudflare = !empty($_SERVER["HTTP_CF_CONNECTING_IP"]);
	$recodeVideo = in_array($_POST["recode-video"] ?? "", array_keys(AVAILABLE_RECODE_FORMATS)) ? $_POST["recode-video"] : null;
	$audioFormat = in_array($_POST["audio-format"] ?? "", array_keys(AVAILABLE_AUDIO_FORMATS)) ? $_POST["audio-format"] : "best";
	$videoFormat = in_array($_POST["video-format"] ?? "", array_keys(AVAILABLE_VIDEO_FORMATS)) ? $_POST["video-format"] : "best";
	$maxFileSize = $_POST["max-filesize"] ?? MAX_FILE_SIZE;
	$audioQuality = strval(max(0, min(9, $_POST["audio-quality"] ?? 5)));
	$extractAudio = boolval($_POST["audio"] ?? "");

	if (!empty($videoUrl))
	{
		// Looking for the unique identifier in the URL.
		// Source: https://gist.github.com/ghalusa/6c7f3a00fd2383e5ef33
		$matches = [];

		preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $matches);

		if (count($matches) > 0)
		{
			// Return of the first result only.
			$videoId = $matches[1];
		}
		else
		{
			$videoOutput = "Incomplete or invalid YouTube video URL.";
		}
	}

	if (!empty($videoId))
	{
		if (!file_exists(OUTPUT_FOLDER))
		{
			// Create the output folder if it does not exist.
			mkdir(OUTPUT_FOLDER);
			mkdir(OUTPUT_FOLDER . "/temp");
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
		$downloadPath = "";

		$downloaderPath = new ExecutableFinder();
		$executablePath = $downloaderPath->find("youtube-dl", null, ["/usr/local/bin"]) ?? $downloaderPath->find("yt-dlp", null, ["/usr/local/bin"]);

		$youtubeDownloader = new YoutubeDl();
		$youtubeDownloader->setBinPath($executablePath ?? "/usr/local/bin/youtube-dl");

		$download_stack = $youtubeDownloader->download(
			Options::create()
				->url("https://www.youtube.com/watch?v=$videoId")
				->format($extractAudio ? null : ($videoFormat . "[filesize<$maxFileSize]"))
				->output(OUTPUT_FORMAT)
				->noPlaylist(true)
				->audioFormat($audioFormat)
				->recodeVideo($cloudflare ? null : $recodeVideo)
				->maxFileSize(MAX_FILE_SIZE)
				->extractAudio($extractAudio)
				->audioQuality($audioQuality)
				->downloadPath(OUTPUT_FOLDER . "/temp")
				->sourceAddress($_SERVER["SERVER_ADDR"])
		);

		foreach ($download_stack->getVideos() as $video)
		{
			if ($video->getError() !== null)
			{
				// Error while downloading/converting.
				$videoOutput = $video->getError();
			}
			else
			{
				// Move and save the downloaded file.
				$fileName = str_replace(OUTPUT_FOLDER . "/temp/", "", $video->getFilename());
				$downloadPath = OUTPUT_FOLDER . "/$fileName";

				rename($video->getFilename(), $downloadPath);
			}
		}
	}
?>

<html lang="en">
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
			You can download videos or extract music from them. <strong>Only videos from YouTube are supported.</strong><br />
			The conversion time by the remote server may differ depending on the video duration and the requested download format.<br />
			Depending on the file size (<strong><?= MAX_FILE_SIZE; ?>B max</strong>) and the speed of your network connection, the download may take several minutes.
		</p>

		<form method="POST">
			<label for="url">URL to the YouTube video:</label>
			<input type="text" autoComplete="off" spellCheck="false" id="url" name="url" placeholder="https://www.youtube.com/watch?v=..." required />

			<label for="audio">Audio only?</label>
			<input type="checkbox" id="audio" name="audio" />

			<details>
				<summary>Advanced options</summary>

				<p>
					If you don't know what you are doing, leave the default settings. For more information, please refer to the YouTube-DL <a href="https://github.com/ytdl-org/youtube-dl#format-selection" target="_blank">documentation</a>.<br />
					Based on the indicated criteria, there may be no download or the file downloaded may not have the specified parameters, this is the normal behavior of YouTube-DL.
				</p>

				<label for="video-format">Video formats (this also covers the video audio)</label>
				<select id="video-format" name="video-format">
					<?php
						foreach (AVAILABLE_VIDEO_FORMATS as $key => $value)
						{
							echo("<option value=\"" . $key . "\">$value</option>");
						}
					?>
				</select>

				<label for="audio-format">Audio-only formats</label>
				<select id="audio-format" name="audio-format">
					<?php
						foreach (AVAILABLE_AUDIO_FORMATS as $key => $value)
						{
							echo("<option value=\"" . $key . "\">$value</option>");
						}
					?>
				</select>

				<label for="recode-video">Recode video<br />If your format is unavailable, you can force a file recoding to a selected format (<strong>this increases processing time and has no guarantee of working</strong>).</label>
				<select id="recode-video" name="recode-video" <?= $cloudflare ? "disabled" : "" ?>>
					<option value="">None</option>

					<?php
						foreach (AVAILABLE_RECODE_FORMATS as $key => $value)
						{
							echo("<option value=\"" . $key . "\">$value</option>");
						}
					?>
				</select>

				<label for="audio-quality">Audio-only quality (0 = better, 9 = worse)</label>
				<input type="range" id="audio-quality" name="audio-quality" min="0" max="9" value="5" step="1">

				<label for="max-filesize">Max file size in bytes (e.g. 50K or 44.6M)<br /><strong>Values above specified server threshold will be ignored during processing.</strong></label>
				<input type="text" id="max-filesize" name="max-filesize" value=<?= MAX_FILE_SIZE ?> placeholder=<?= MAX_FILE_SIZE ?> />
			</details>

			<input type="submit" value="Download" />
		</form>

		<!-- Download link -->
		<?php if (!empty($downloadPath)): ?>
			📥 <a href="<?= "$downloadPath?time=" . time() ?>" download>Download doesn't start by itself? Please click here</a>.
		<?php endif; ?>

		<!-- Error output -->
		<?php if (!empty($videoOutput)):  ?>
			<h3>⚠️ Error output ⚠️</h3>

			<p><?= $videoOutput ?></p>
		<?php endif; ?>
	</body>
</html>
