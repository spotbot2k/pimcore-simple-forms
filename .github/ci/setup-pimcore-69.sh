#!/bin/bash

set -eu

cp -r .github/ci/files-pimcore-69/app app
cp -r .github/ci/files-pimcore-69/bin bin
cp -r .github/ci/files-pimcore-69/web web
mkdir -p var/config

mkdir var/config
cp .github/ci/files-pimcore-69/extensions.php var/config/extensions.php

composer require codeception/codeception:2.4.5 --no-update
composer require pimcore/pimcore:6.9.6
composer require spotbot2k/pimcore-simple-forms:dev-master

php bin/console pimcore:bundle:install SimpleFormsBundle
php bin/console pimcore:bundle:enable SimpleFormsBundle