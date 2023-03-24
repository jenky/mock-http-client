# PSR-18 Mock HTTP Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Github Actions][ico-gh-actions]][link-gh-actions]
[![Codecov][ico-codecov]][link-codecov]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require jenky/atlas-mock-client
```

## Usage

```php
use Jenky\Atlas\Mock\MockClient;

$client = new MockClient();
$response = $client->sendRequest($request);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email jenky.w0w@gmail.com instead of using the issue tracker.

## Credits

- [Lynh](https://github.com/jenky)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jenky/atlas-mock-client.svg?style=for-the-badge
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge
[ico-travis]: https://img.shields.io/travis/jenky/atlas-mock-client/master.svg?style=for-the-badge
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/jenky/atlas-mock-client.svg?style=for-the-badge
[ico-code-quality]: https://img.shields.io/scrutinizer/g/jenky/atlas-mock-client.svg?style=for-the-badge
[ico-gh-actions]: https://img.shields.io/github/actions/workflow/status/jenky/atlas-mock-client/testing.yml?branch=main&label=actions&logo=github&style=for-the-badge
[ico-codecov]: https://img.shields.io/codecov/c/github/jenky/atlas-mock-client?logo=codecov&style=for-the-badge
[ico-downloads]: https://img.shields.io/packagist/dt/jenky/atlas-mock-client.svg?style=for-the-badge

[link-packagist]: https://packagist.org/packages/jenky/atlas-mock-client
[link-travis]: https://travis-ci.org/jenky/atlas-mock-client
[link-scrutinizer]: https://scrutinizer-ci.com/g/jenky/atlas-mock-client/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/jenky/atlas-mock-client
[link-gh-actions]: https://github.com/jenky/jenky/atlas-mock-client
[link-codecov]: https://codecov.io/gh/jenky/atlas-mock-client
[link-downloads]: https://packagist.org/packages/jenky/atlas-mock-client

