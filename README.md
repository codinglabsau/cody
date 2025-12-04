# Cody orchestrates and runs AI workflows in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codinglabsau/cody.svg?style=flat-square)](https://packagist.org/packages/codinglabsau/cody)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/codinglabsau/cody/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/codinglabsau/cody/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/codinglabsau/cody/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/codinglabsau/cody/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/codinglabsau/cody.svg?style=flat-square)](https://packagist.org/packages/codinglabsau/cody)

Cody is an AI Agent that allows you to run AI workflows on-demand, or on a schedule that you define, right inside your
Laravel app.

## Features

- Enscapsulate the project AI strategy using prompts defined in markdown
- Manage git worktrees in your local environment through simple artisan commands
- PRs submitted automatically to GitHub with AI-generated summaries
- Workflows can be invoked on-demand or scheduled as recurring background tasks

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

The project `composer.json` should include a Cody setup script, which is executed on git worktrees to get the branch
ready for agent workflows. For example:

```json
{
    "scripts": {
        "ai": [
            "composer install",
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
            "@php artisan key:generate",
            "@php artisan boost:update",
            "npm ci"
        ]
    }
}
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

There are two main ways to use Cody Agent; on-demand, or on a schedule.

### On-demand

Coding can be invoked with `php artisan cody <branch-name> --prompt='<prompt>'`.

For example:

```bash
php artisan cody migrate-to-wayfinder --prompt='Switch from ziggy.js to Laravel Wayfinder. Update all references.'
```

This command will:

1. Create a new git worktree at `../<app-name>-migrate-to-wayfinder`
2. Call `composer ai` script on the worktree
3. Call Codex CLI with the prompt to complete the task
4. Again call Codex to create a succint commit message and PR description
5. Commit, push, and create a PR on GitHub using the PR template (when available)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Create an issue to notify us of any security vulnerabilities.

## Credits

- [stevethomas](https://github.com/stevethomas)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
