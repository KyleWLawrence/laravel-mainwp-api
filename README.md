# Laravel MainWP

This package provides integration with the MainWP API. It currently only supports sending a chat message.

## Installation

You can install this package via Composer using:

```bash
composer require kylewlawrence/laravel-mainwp-api
```

The facade is automatically installed.

```php
MainWP::get('sites', ['per_page' => 100]);
```

## Configuration

To publish the config file to `app/config/mainwp-laravel.php` run:

```bash
php artisan vendor:publish --provider="KyleWLawrence\MainWP\Providers\MainWPServiceProvider"
```

Set your configuration using **environment variables**, either in your `.env` file or on your server's control panel:

- `MAINWP_DOMAIN`

The fully qualified domain name where MainWP is hosted.

- `MAINWP_CONSUMER_KEY`

The API access consumer auth. You can create one as described here: `https://kb.mainwp.com/docs/mainwp-rest-api/`

- `MAINWP_CONSUMER_SECRET`

Accompanying the consumer key.

- `MAINWP_DRIVER` _(Optional)_

Set this to `null` or `log` to prevent calling the MainWP API directly from your environment.

## Contributing

Pull Requests are always welcome here. I'll catch-up and develop the contribution guidelines soon. For the meantime, just open and issue or create a pull request.

## Usage

### Facade

The `MainWP` facade acts as a wrapper for an instance of the `MainWP\Http\HttpClient` class.

### Dependency injection

If you'd prefer not to use the facade, you can instead inject `KyleWLawrence\MainWP\Services\MainWPService` into your class. You can then use all of the same methods on this object as you would on the facade.

```php
<?php

use KyleWLawrence\MainWP\Services\MainWPService;

class MyClass {

    public function __construct(MainWPService $mainwp_service) {
        $this->mainwp_service = $mainwp_service;
    }

    public function getSites() {
        $this->mainwp_service->get('sites/all-sites');
    }

}
```

This package is available under the [MIT license](http://opensource.org/licenses/MIT).
