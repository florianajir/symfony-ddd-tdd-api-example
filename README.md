# symfony-ddd-tdd-api-example

![CI](https://github.com/florianajir/symfony-ddd-tdd-api-example/workflows/CI/badge.svg)
[![codecov](https://codecov.io/gh/florianajir/symfony-ddd-tdd-api-example/branch/main/graph/badge.svg)](https://codecov.io/gh/florianajir/symfony-ddd-tdd-api-example)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. If this is the first install run `make init` or `make up` the next times to avoid build time
3. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
4. Run `make down` to stop the Docker containers.

## Features

* DDD and hexagonal architecture
* Production, development and CI ready
* PHPUnit integration
* Native [XDebug](docs/xdebug.md) integration
* Make commands for management
* User registration and login
* JWT support

## Docs

1. [Build options](docs/build.md)
2. [Debugging with Xdebug](docs/xdebug.md)
3. [TLS Certificates](docs/tls.md)
4. [Using a Makefile](docs/makefile.md)
5. [Troubleshooting](docs/troubleshooting.md)

## Credits

Created by Florian Ajir.
