exponential-backoff
===================

[![Build Status](https://travis-ci.org/eoko/exponential-backoff.svg?branch=master)](https://travis-ci.org/eoko/exponential-backoff)
[![Code Climate](https://codeclimate.com/github/eoko/exponential-backoff/badges/gpa.svg)](https://codeclimate.com/github/eoko/exponential-backoff)
[![Eoko Public Channel](http://slackin.eoko.fr/badge.svg)](http://slackin.eoko.fr/)

Overview
--------

This service create a loop and delay cleanly the next loop while you callable throw an exception. It's perfect when you have to
call service with an unpredictable result.

Comments
--------

This method should absolutely not be used in front. By delaying the completion of a script opens your system 
up to Denial of Service (DoS) attacks as it ties up server resources for the duration of the script, putting you at risk 
for exceeding your available PHP processes, database connections, and other server resources.

Requirements
------------
  
Please see the [composer.json](composer.json) file.

Installation
------------

Run the following `composer` command:

```console
$ composer require "eoko/exponential-backoff"
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "eoko/exponential-backoff": "master-dev"
}
```

And then run `composer update` to ensure the module is installed.

Get Started
-----------

@todo

Events
------
    
|      Event Const     |             Event Name              |               Description              |
| -------------------- | ----------------------------------- | -------------------------------------- |
| EVENT_PRE_CALL       | event.exponential-backoff.pre       | Triggered before anything              |
| EVENT_POST_CALL      | event.exponential-backoff.post      | Triggered when jobs is done            |
| EVENT_EXCEPTION_CALL | event.exponential-backoff.exception | Triggered when an exception is thrown  |
| EVENT_SLEEP_CALL     | event.exponential-backoff.sleep     | Triggered when the function will sleep |
| EVENT_RETRY_CALL     | event.exponential-backoff.retry     | Triggered when the funciton will retry |
| EVENT_END_CALL       | event.exponential-backoff.end       | Triggered when there is no more retry  |

The event list can be used as follow :

    $callback = function(Event $e) {
        ob_end_clean();
        echo $e->getParams()->__toString();
        ob_start();
    };

    $eventManager->attach(ExponentialBackoff::EVENT_PRE_CALL, $callback);
    $eventManager->attach(ExponentialBackoff::EVENT_POST_CALL, $callback);
    $eventManager->attach(ExponentialBackoff::EVENT_EXCEPTION_CALL, $callback);
    $eventManager->attach(ExponentialBackoff::EVENT_SLEEP_CALL, $callback);
    $eventManager->attach(ExponentialBackoff::EVENT_RETRY_CALL, $callback);
    $eventManager->attach(ExponentialBackoff::EVENT_END_CALL, $callback);


Credits
-------

@todo
