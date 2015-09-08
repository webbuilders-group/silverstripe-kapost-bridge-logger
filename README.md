Kapost Bridge Logger
=================
A wrapper for our SilverStripe Kapost Bridge module that logs all incoming requests to the kapost service.

## Maintainer Contact
* Ed Chipman ([UndefinedOffset](https://github.com/UndefinedOffset))

## Requirements
* SilverStripe CMS 3.1.x
* [Kapost Bridge](https://github.com/webbuilders-group/silverstripe-kapost-bridge)


## Installation
__Composer (recommended):__
```
composer require webbuilders-group/silverstripe-kapost-bridge-logger
```


If you prefer you may also install manually:
* Download the module from here https://github.com/webbuilders-group/silverstripe-kapost-bridge-logger/archive/master.zip
* Extract the downloaded archive into your site root so that the destination folder is called kapost-bridge-logger, opening the extracted folder should contain _config.php in the root along with other files/folders
* Run dev/build?flush=all to regenerate the manifest


## Configuration Options
```yml
KapostBridgeLog:
    log_expire_days: 30 #Number of days that logs are kept

KapostBridgeLogViewer:
    log_page_length: 20 #Number of logs to include per page
```

## Extension Points
There is one extension point that allows you to hook into the lookup process for the destination object. This extension point is called ``updateObjectLookup`` and is on the KapostBridgeLog class. The extension point is given one argument which is the Kapost Reference ID. Extensions using this extension point should return an object (or null) with the CMSEditLink method defined if you want a link to appear on the log. The CMSEditLink method must return a url to the edit page for that object. The first extension to return an object is used.
