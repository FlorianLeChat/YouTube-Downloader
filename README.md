# 📺 Youtube Downloader

## In French

### Introduction

Ce petit site Internet permet de fournir une interface simple et fonctionnelle aux personnes voulant se servir de [YouTube-DL](https://github.com/ytdl-org/youtube-dl) (ou [YT-DLP](https://github.com/yt-dlp/yt-dlp)) pour télécharger ou extraire l'audio des vidéos YouTube sous différents formats et différentes qualités. Pour accélérer le processus de convertion et de téléchargement, le serveur peut garder une copie des fichiers convertis pour pouvoir l'envoyer aux clients si nécessaire.

### Installation

> [!WARNING]
> Le déploiement en environnement de production (**avec ou sans Docker**) nécessite un serveur Web déjà configuré comme [Nginx](https://nginx.org/en/), [Apache](https://httpd.apache.org/) ou [Caddy](https://caddyserver.com/) pour servir les scripts PHP.

- Installer [PHP LTS](https://www.php.net/downloads.php) (>8.1 ou plus) ;
- Installer [Python 3](https://www.python.org/downloads/), [PIP](https://pypi.org/project/pip/), [FFmpeg](https://www.ffmpeg.org/download.html) et [YouTube Downloader](https://github.com/yt-dlp/yt-dlp/wiki/Installation) (YT-DLP) ;
- Installer les extensions PHP additionnelles suivantes : `zip`, `opcache` ;
- Installer les dépendances du projet avec la commande `composer install` ;
- Utiliser un serveur Web pour servir les scripts PHP et les fichiers statiques.

> [!TIP]
> Pour tester le projet, vous pouvez utiliser [Docker](https://www.docker.com/). Une fois installé, il suffit de lancer l'image Docker de développement à l'aide de la commande `docker compose up --detach --build`. Le site devrait être accessible à l'adresse suivante : http://localhost/. 🐳

> [!CAUTION]
> L'image Docker peut être déployée en production, mais cela **nécessite des connaissances approfondies pour déployer, optimiser et sécuriser correctement votre installation**, afin d'éviter toute conséquence indésirable. ⚠️

## In English

### Introduction

This simple website provides a convenient and functional interface for people looking to use [YouTube-DL](https://github.com/ytdl-org/youtube-dl) (or [YT-DLP](https://github.com/yt-dlp/yt-dlp)) to download or extract audio from YouTube videos in different formats and quality levels. In order to speed up the conversion and upload process, the server can keep a copy of the converted files to send to the clients if necessary.

### Setup

> [!WARNING]
> Deployment in a production environment (**with or without Docker**) requires a pre-configured web server such as [Nginx](https://nginx.org/en/), [Apache](https://httpd.apache.org/), or [Caddy](https://caddyserver.com/) to serve PHP scripts.

- Install [PHP LTS](https://www.php.net/downloads.php) (>8.1 or higher) ;
- Install [Python 3](https://www.python.org/downloads/), [PIP](https://pypi.org/project/pip/), [FFmpeg](https://www.ffmpeg.org/download.html) and [YouTube Downloader](https://github.com/yt-dlp/yt-dlp/wiki/Installation) (YT-DLP) ;
- Install the following additional PHP extensions: `zip`, `opcache` ;
- Install project dependencies using `composer install` ;
- Use a web server to serve PHP scripts and static files.

> [!TIP]
> To try the project, you can use [Docker](https://www.docker.com/) installed. Once installed, simply start the development Docker image with `docker compose up --detach --build` command. The website should be available at http://localhost/. 🐳

> [!CAUTION]
> The Docker image can be deployed in production, but **this requires advanced knowledge to properly deploy, optimize, and secure your installation**, in order to avoid any unwanted consequences. ⚠️

![image](https://user-images.githubusercontent.com/26360935/190904189-22ef0ab2-5f48-4b80-827d-2d44c0498453.png)