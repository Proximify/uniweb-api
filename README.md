<p align="center">
  <img src="docs/assets/uniweb_logo.svg" width="200px" alt="uniweb API logo">
</p>

# UNIWeb's API Definition and Client

The purpose of the API is to integrate UNIWeb with other systems within your organization. The API provides secure read/write access to information stored by UNIWeb, and it provides a mechanism to eliminate the need to duplicate data.

The UNIWeb API provides:

-   An interface that allows you to control who has access to your institution's data through our API.
-   A means by which to securely read and update your institution's information.
-   Rich data in simple, straightforward JSON for maximum readability and reusability.
-   The choice to pre-filter the requested data, to obtain just the subset of information in which you are interested.

**Learn more:** [Complete documentation of the UNIWeb API](docs/uniweb-api.md).

## API Client

An example reference client for the Uniweb API is written in PHP and can be installed via [Composer](https://getcomposer.org/).

```zsh
$ composer require proximify/uniweb-api
```

### Testing

A simple way to test the client is to use the PHP built-in web server. To launch the built-in web server, change directory to the `www` folder within the website project, and start the web server there.

```zsh
$ cd vendor/proximify/uniweb-api/www/ && php -S localhost:8000
```

While the PHP web server is running, the URL `http://localhost:8000` should display the website.
