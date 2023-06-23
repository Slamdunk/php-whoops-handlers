# Slam\WhoopsHandlers

[![Latest Stable Version](https://img.shields.io/packagist/v/slam/php-whoops-handlers.svg)](https://packagist.org/packages/slam/php-whoops-handlers)
[![Downloads](https://img.shields.io/packagist/dt/slam/php-whoops-handlers.svg)](https://packagist.org/packages/slam/php-whoops-handlers)
[![Integrate](https://github.com/Slamdunk/php-whoops-handlers/workflows/CI/badge.svg)](https://github.com/Slamdunk/php-whoops-handlers/actions)

Additional Handlers for [Whoops](https://github.com/filp/whoops).

## Installation

`composer require slam/php-whoops-handlers`

## Available Handlers

1. [`BodilessHtmlHandler`](https://github.com/Slamdunk/php-whoops-handlers/blob/master/lib/BodilessHtmlHandler.php): render a 500 page without any detail, useful for Production
2. [`EmailHandler`](https://github.com/Slamdunk/php-whoops-handlers/blob/master/lib/EmailHandler.php): craft a body to give to your emailer function