[![Build Status](https://travis-ci.org/renan-taranto/csv-parser.svg?branch=master)](https://travis-ci.org/renan-taranto/csv-parser)
[![Coverage Status](https://coveralls.io/repos/github/renan-taranto/csv-parser/badge.svg?branch=master)](https://coveralls.io/github/renan-taranto/csv-parser?branch=master)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

CSV Parser
======
An easy to use CSV parser

### Features
- Converts CSV to arrays and arrays indexed by the first row cells (headers);   
- Automatically guesses CSV fields delimiter;
- Provides limit and offset options for parsing;
- Performance wise parsing while using as an iterator.

### Requirements
- PHP 7.0 or later.

### Installation
```
composer require taranto/csv-parser
```

### Usage
Parsing a CSV file to an array:
```php
$csvParser = new CsvParser('file.csv');
$csvAsArray = $csvParser->getCsvAsArray();
```
Parsing a CSV file to an array indexed by the first row cells (headers):
```php
$csvParser = new CsvParser('file.csv');
$csvAsArray = $csvParser->getCsvAsAssociativeArray();
```
Performance wise usage (good for large files):
- Simple arrays
```php
$csvParser = new CsvParser('file.csv');
$csvAsArray = [];
foreach ($csvParser as $row) {
    $csvAsArray[] = $row;
}
```
- Associative arrays
```php
$csvParser = new CsvParser('file.csv', true);
$csvAsArray = [];
foreach ($csvParser as $row) {
    $csvAsArray[] = $row;
}
```
### Example
Given the CSV
```
| name  | birthdate   | 
| John  |  1985-02-03 | 
|  Kim  |  1976-05-04 | 
|  Suzy |  1991-04-02 |
|  Tom  |  1970-01-03 |
```
Parsing to an associtive array with offset(1) and limit(2):
```php
$csvParser = new CsvParser('file.csv');
$csvAsArray = $csvParser->getCsvAsAssociativeArray(1, 2);
```
Returns:
```php
[
    ["name" => "Kim", "birthdate" => "1976-05-04"]
    ["name" => "Suzy", "birthdate" => "1991-04-02"]
]
```
### Author

* **Renan Taranto** - *renantaranto@gmail.com*

### License

This project is licensed under the MIT License - see the LICENSE.txt file for details
