# Symfony CSV metrics aggregator bundle (demo task)

## Installation
1. Add the next lines to your `composer.json`
```json
...
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/imissyouso/csv_aggregator_bundle"
    }
]
...
```
```json
...
"minimum-stability": "dev"
...
```
2. Run
```bash
$ composer require imissyouso/csv_aggregator_bundle
```

## Usage
```bash
$ bin/console csv:aggregate <sourcePath> <outPath> [<header>]

Arguments:
   sourcePath            CSV directory path
   outPath               Result CSV file path
   header                CSV header pattern [default: "date;A;B;C"]
```
For example:
```bash
$ bin/console csv:aggregate data result.csv
```
by this way it will scan the `data` directory. The output file will be named `result.csv`.

## Comments
- покрыты тестами **критичные** части кода (по заданию);

## Original task
https://gist.github.com/pavelkdev/435244a2c2e3a9d8dcb2353511fd9dad

## Author
Andrey Vorobyev
