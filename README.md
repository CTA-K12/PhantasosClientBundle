# Phantasos Client Bundle
The purpose of this Symfony bundle is to provide a set of tools to make connecting to our simple media server, [Phantasos](https://github.com/MESD/Phantasos)

## Getting Started
To install the bundle with your Symfony application, use composer to add the client to your application.
```bash
$ composer require mesd/phantasos-client-bundle "~0.1"
```
Then add the bundle to your app kernel.
```php
$bundles = array(
  ...
  new Mesd\PhantasosClientBundle\MesdPhantasosClientBundle()
);
```
