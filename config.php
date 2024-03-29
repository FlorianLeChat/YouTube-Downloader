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
	// https://github.com/yt-dlp/yt-dlp#format-selection (--format)
	const AVAILABLE_VIDEO_FORMATS = [
		"best*" => "Best (default)",
		"mp4" => "MP4",
		"webm" => "WebM",
		"flv" => "FLV",
		"3gp" => "3GP",
		"ogg" => "OGG",
		"mkv" => "MKV",
		"avi" => "AVI"
	];

	// Available audio formats.
	// https://github.com/yt-dlp/yt-dlp#post-processing-options (--audio-format)
	const AVAILABLE_AUDIO_FORMATS = [
		"best" => "Best (default)",
		"mp3" => "MP3",
		"m4a" => "M4A",
		"wav" => "WAV",
		"flac" => "FLAC",
		"aac" => "AAC",
		"alac" => "ALAC",
		"opus" => "Opus",
		"vorbis" => "Vorbis"
	];

	// Available recode formats.
	// https://github.com/yt-dlp/yt-dlp#post-processing-options (--recode-video)
	const AVAILABLE_RECODE_FORMATS = [
		"avi" => "AVI",
		"flv" => "FLV",
		"gif" => "GIF",
		"mkv" => "MKV",
		"mov" => "MOV",
		"mp4" => "MP4",
		"webm" => "WebM",
		"aac" => "AAC",
		"aiff" => "AIFF",
		"alac" => "ALAC",
		"flac" => "FLAC",
		"m4a" => "M4A",
		"mka" => "MKA",
		"mp3" => "MP3",
		"ogg" => "OGG",
		"opus" => "Opus",
		"vorbis" => "Vorbis",
		"wav" => "WAV"
	];
?>