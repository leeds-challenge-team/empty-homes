# Empty Homes

## Installation

### Development Environment

This software uses [Vagrant](https://www.vagrantup.com) and [PuPHPet](https://puphpet.com) to create a virtual development environment with most of what you need to run.

* Make sure you have [VirtualBox](https://www.virtualbox.org) installed.
* Make sure you have [Vagrant](https://docs.vagrantup.com/v2/installation/) installed.
* Clone the repository.
* `vagrant up`
* This may take a while, it will need to download and provision an entire OS and server stack. Grab a cup of tea and watch the magic happen.
* Add `emptyhomes.dev` to your hosts file with the IP `192.168.58.59`.

### PHP Packages

* `vagrant ssh` to connect to the development environment.
* `cd /site` to get to the right location.
* `composer install` to get all the right packages to do things.
