file-object-plus
================

A simple extension of the PHP class [SplFileObject](http://php.net/manual/en/class.splfileobject.php)

Build status:
- master: [![Build Status](https://travis-ci.org/dcarbone/file-object-plus.svg?branch=master)](https://travis-ci.org/dcarbone/file-object-plus)

## Basics:

This class is a very simple extension of the base PHP [SplFileObject](http://php.net/manual/en/class.splfileobject.php).
As such, it has all the same functionality as the base class with a few minor additions.

### Countable Interface

I have implemented the [Countable](http://php.net/manual/en/class.countable.php) interface into this class.
It utilizes my [FileHelper](https://github.com/dcarbone/helpers/blob/master/src/FileHelper.php) helper class
to determine the count

To use, simply execute:

```php
$fileObject = new DCarbone\FileObjectPlus('myfile.txt');
$count = count($fileObject);
echo $count;
```

To count lines that contain a term, execute:

```php
$fileObject = new DCarbone\FileObjectPlus('myfile.txt');
$count = $fileObject->countLinesContaining('my term');
echo $count;
```

### Pagination

This class also implements some very simple pagination methods, modeled closely to how you would
specify returning a portion of a database table.

To get a portion of a file irrespective of line content:

```php
$fileObject = new DCarbone\FileObjectPlus('myfile.txt');
$offset = 0;
$limit = 25;
$lines = $fileObject->paginateLines($offset, $limit);
var_dump($lines);
```

By default, blank lines are also returned.  You may alternatively ignore these by passing in 4 parameters:

```php
$fileObject = new \DCarbone\FileObjectPlus('myfile.txt');
$offset = 0;
$limit = 25;
$search = null;
$includeEmpty = false;
$lines = $fileObject->paginateLines($offset, $limit, $search, $includeEmpty);
var_dump($lines);
```

If you wish to paginate through a file only matching lines that contain a certain term:

```php
$fileObject = new \DCarbone\FileObjectPlus('myfile.txt');
$offset = 0;
$limit = 25;
$search = 'my term';
$lines = $fileObject->paginateLines($offset, $limit, $search);
```

*Note*: When searching, the fourth parameter is ignored

*Note*: Both pagination functions currently reset the underlying SplFileObject's internal line pointer.