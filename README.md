exponential-backoff
===================

[![Build Status](https://travis-ci.org/eoko/exponential-backoff.svg?branch=master)](https://travis-ci.org/eoko/exponential-backoff)
[![Code Climate](https://codeclimate.com/github/eoko/exponential-backoff/badges/gpa.svg)](https://codeclimate.com/github/eoko/exponential-backoff)

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


Credits
-------

@todo
