#!/bin/bash

set -e

composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-suggest --no-scripts --prefer-dist --no-ansi --no-plugins --no-scripts
