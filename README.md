# FreshRSS - "Comics in Feed" extension

This FreshRSS extension allows you to directly comics like [The awkward yeti](https://theawkwardyeti.com/), [Buttersafe](https://www.buttersafe.com/) and more comics within your FreshRSS installation.

To use it, upload the ```freshrss-comicsinfeed``` directory to the FreshRSS `./extensions` directory on your server and enable it on the extension panel in FreshRSS.

I will probably be adding more comics in the future (when/if I start reading them) but feel free to add your own with a pull request.


## Requirements

This FreshRSS extension uses the PHP extension [DOM](http://php.net/dom) and [XML](http://php.net/xml).

As those are requirements by [FreshRSS](https://github.com/FreshRSS/FreshRSS) itself, you should be good to go.


## Installation

To install an extension, download the extension archive first and extract it on your PC (or directly on your host). Then, upload/move the folder you want on your server. Extensions must be in the ./extensions directory of your FreshRSS installation.

Then go to your FreshRSS installation and activate the extension in the extension panel - https://localhost/FreshRSS/p/i/?c=extension - and activate it.

## About FreshRSS

[FreshRSS](https://freshrss.org/) is a great self-hosted RSS Reader written in PHP, which is can also be found here at [GitHub](https://github.com/FreshRSS/FreshRSS).

More extensions can be found at [FreshRSS/Extensions](https://github.com/FreshRSS/Extensions).

## To add more comics

Essentially create a new file for your comic on the folder `./comics/` see the other files there for examples, add it to be loaded on the './comics/load.php' file and add the comic to the list of comics on the `./comics/loader.php` file.

Edit the `extension.php` file to make the match for the feed and the new function to generate the new image source.

Feel free for any pull request or to make a request for a new comic (if I have time I will add it).

## Currently supported comics

- [The awkward yeti](https://theawkwardyeti.com/)
- [Buttersafe](https://www.buttersafe.com/)
- [buni](https://www.bunicomic.com/)


## Changelog

#### 1.1

2 September 2023
  - Parses comics images from [buni](https://www.bunicomic.com/) feed to display fully in FreshRSS

#### 1.0

20 August 2023
 - Parses comics images from [The awkward yeti](https://theawkwardyeti.com/) feed to display fully in FreshRSS
 - Parses comics images from [Buttersafe](https://www.buttersafe.com/) feed to display fully in FreshRSS
