 ![https://img.shields.io/static/v1?style=flat-square&color=6428B4&message=X&label=Pimcore](https://img.shields.io/static/v1?style=flat-square&color=6428B4&message=X&label=Pimcore)
 ![https://img.shields.io/static/v1?style=flat-square&color=6428B4&message=6.9&label=Pimcore](https://img.shields.io/static/v1?style=flat-square&color=6428B4&message=6.9&label=Pimcore)
 [![](https://img.shields.io/packagist/v/spotbot2k/pimcore-simple-forms.svg?style=flat-square&color=F28D1A&logoColor=white)](https://packagist.org/packages/spotbot2k/pimcore-simple-forms)

# Simple Form Builder for Pimcore

Create and modify simple forms in [Pimcore](https://github.com/pimcore/pimcore) backend with no code needed. The aim is to provide a simple UI that will be easy enough to be used by a Pimcore newbie.

## Installation

``` bash
composer require spotbot2k/pimcore-simple-forms
```

## What can it do

- Visual editing of forms
- Upload assets
- Spam protection using honeypot
- Simple validation of the fields in backend and frontend
- Send the submitted data via E-Mail
- Send the submitted data to a custom URL/path so you could
  - process the data in a custom controller
  - create login forms
- Parse to/cc/bcc from the form input

## Configuration

- [Basic Configuration](docs/01_Basic_Configuration.md)
- [Advanced Usage](docs/02_Advanced_Usage.md)
  - [Using a custom controller](docs/021_Using_custom_controller.md)
  - Creating a login form
  - [Using events](docs/023_Using_events.md)
