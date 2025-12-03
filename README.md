# Cody orchestrates and runs AI workflows in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codinglabsau/cody.svg?style=flat-square)](https://packagist.org/packages/codinglabsau/cody)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/codinglabsau/cody/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/codinglabsau/cody/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/codinglabsau/cody/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/codinglabsau/cody/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/codinglabsau/cody.svg?style=flat-square)](https://packagist.org/packages/codinglabsau/cody)

Cody is an AI Agent that allows you to run AI workflows on-demand, or on a schedule that you define.

## Features

- Enscapsulates your AI strategy through versioned application configuration
- Creates git worktrees to your local environment, executes prompts, and creates PRs on GitHub all in the background
- Workflows can be invoked on-demand, or scheduled as recurring tasks to proactively chip away

## Installation

You can install the package via composer:

```bash
composer require codinglabsau/cody
```

In addition to the package, the following commands can be run as required:

```bash
# dependencies
npm install -g @openai/codex
brew install gh

# authenticate with codex
codex login

# authenticate with GitHub
gh auth login

# authenticate with Linear
codex mcp add --url https://mcp.linear.app/mcp linear
codex mcp login linear -c experimental_use_rmcp_client=true
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="cody-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$cody = new Codinglabs\Cody();
echo $cody->echoPhrase('Hello, Codinglabs!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [stevethomas](https://github.com/stevethomas)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
