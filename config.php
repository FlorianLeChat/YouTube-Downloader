<?php
	// Maximum file size to download (in bytes).
	const MAX_FILE_SIZE = "100M";

	// Output folder for the downloaded files.
	const OUTPUT_FOLDER = "output";

	// Output format for the downloaded files.
	const OUTPUT_FORMAT = "%(title)s-%(id)s.%(ext)s";

	// Delete all files in the output folder before downloading a new video.
	const DONT_KEEP_FILES = true;

	// Available video formats.
	const AVAILABLE_VIDEO_FORMATS = [
		"best" => "Best (default)",
		"mp4" => "MP4",
		"webm" => "WebM",
		"flv" => "FLV",
		"3gp" => "3GP",
		"ogg" => "OGG",
		"mkv" => "MKV",
		"avi" => "AVI"
	];

	// Available audio formats.
	const AVAILABLE_AUDIO_FORMATS = [
		"best" => "Best (default)",
		"mp3" => "MP3",
		"m4a" => "M4A",
		"wav" => "WAV",
		"flac" => "FLAC",
		"aac" => "AAC",
		"opus" => "Opus",
		"vorbis" => "Vorbis"
	];
?>