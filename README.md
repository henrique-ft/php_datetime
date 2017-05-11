## Install

Via Composer

``` bash
$ composer require blacktools/datetime
```

## Usage

``` php

require 'vendor/autoload.php';

use Blacktools\DateTime\TimeMachine;

var_dump(TimeMachine::interval('00:00:00','24:00:00'));

/*
  array(24) {
  [0] =>
  string(8) "00:00:00"
  [1] =>
  string(8) "01:00:00"
  [2] =>
  string(8) "02:00:00"
  [3] =>
  string(8) "03:00:00"
  [4] =>
  string(8) "04:00:00"
  [5] =>
  string(8) "05:00:00"
  [6] =>
  string(8) "06:00:00"
  [7] =>
  string(8) "07:00:00"
  ...

```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email hriqueft@gmail.com instead of using the issue tracker.

## Credits

- [Henrique Fernandez Teixeira][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/:vendor/:package_name/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/:vendor/:package_name.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/:vendor/:package_name.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/:vendor/:package_name.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-travis]: https://travis-ci.org/:vendor/:package_name
[link-scrutinizer]: https://scrutinizer-ci.com/g/:vendor/:package_name/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/:vendor/:package_name
[link-downloads]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/:author_username
[link-contributors]: ../../contributors
