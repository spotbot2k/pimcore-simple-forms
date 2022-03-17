#!/bin/bash

set -eu

mkdir -p var/config
mkdir -p bin

cp -r .github/ci/config/. config
cp -r .github/ci/Kernel.php kernel/Kernel.php
cp .github/ci/.env ./
