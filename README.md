file-object-plus
================

A simple extension of the PHP class SplFileObject

Build status:
- master: [![Build Status](https://travis-ci.org/dcarbone/file-object-plus.svg?branch=master)](https://travis-ci.org/dcarbone/file-object-plus)
- 0.1.5: [![Build Status](https://travis-ci.org/dcarbone/file-object-plus.svg?branch=0.1.5)](https://travis-ci.org/dcarbone/file-object-plus)

## Basics:

This class is a very simple extension of the base PHP [SplFileObject](!http://php.net/manual/en/class.splfileobject.php).
As such, it has all the same functionality as the base class with a few minor additions.

### Line Counting

One of the more tedious things to do in PHP is count lines in a file.  This library attempts to help in that area with a
few helper methods:

```php
/**
 * @return int
 */
public function getLineCount();

/**
 * @param string|int|bool|float $string
 * @return int
 * @throws \InvalidArgumentException
 */
public function getLineCountLike($string);
```

#### getLineCount

This method will return an integer representing the number of lines found in a file, based upon one of the following
`shell_exec` commands:

- Windows
    - `(int)trim(shell_exec('type "'.$this->getRealPath().'" | find /c /v "~~~"'));`
- Linux-based
    - `$this->lineCount = (int)trim(shell_exec('wc -l "'.$this->getRealPath().'"'));`

**Note:** If you have suggestions on how to improve this line counting logic, please let me know.  I simply went with what
I found works.

**Note:** Calling either of these methods resets the object's internal line pointer, so be aware of that.

#### getLineCountLike($string)

This method operates in a slightly more complicated manor.  It will iterate over every line in a file (meaning performance
could suffer if you have a particularly large file) and executes the PHP method [stripos](!http://php.net/manual/en/function.stripos.php)
function with the passed in `$string` value.  If result is !== false, that line is added to the response array.

### Pagination

I had a need to be able to represent very large files (think Apache-logs on a very active web app) as quickly as possible.
To that end, I have added the following method:

```php
/**
 * @param int $offset
 * @param int $limit
 * @param int|float|string|null $search
 * @return array
 * @throws \InvalidArgumentException
 */
public function paginateLines($offset = 0, $limit = 25, $search = null);
```

This method does exactly what you'd think: iterates over lines in a file, returning an array of strings representing
the lines that are > $offset, < $limit, and optionally match $search.

**Note:** Calling either of these methods resets the object's internal line pointer, so be aware of that.