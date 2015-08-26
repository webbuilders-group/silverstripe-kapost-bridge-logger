Kapost Bridge Logger
=================
A wrapper for our SilverStripe Kapost Bridge module that logs all incoming requests.

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

```
