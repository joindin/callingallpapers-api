# Welcome!

So you are interested in contributing to the callingallpapers-API? That's great!

But how to start? Have a look at the [issue-list](https://github.com/joindin/callingallpapers-api/issues).
We try to mark easy things as "[Easy Pick](https://github.com/joindin/callingallpapers-api/labels/Easy%20Fix)".
There's something missing in your eyes? Feel free to add it. Either by sharing
your ideas or concerns via a new issue or by adding a Pull-Request.

For that you'll want to setup the API on your dev-environment. To do so:

## Setup

1. Fork the project on github
2. Clone your repository onto your dev-machine.
  ```
  git clone git@github.com:<your github-username>/callingallpapers-api.git
  ```
3. Move to the newly created folder:
  ```
  cd callingallpapers-api
  ```
4. Install the dependencies by running ```composer update```. This will also
    include all development-stuff that is needed.
5. Create the settings-file by copying the provided template
    ```
    cp config/settings.php.dist config/settings.php
    ```
    (you can edit it if you want to but the default settings should be OK)
6. Start PHPs internal webserver
    ```
    php -S localhost:8000 -t public
    ```
7. Point your browser to [your API](http://localhost:8000/v1/cfp)

Now you should be up and running to contribute to the API. Your API does not
contain any data at that point so you might want to add some stuff at this point.

## Coding-Guidelines

We follow the [PSR-2](http://www.php-fig.org/psr/psr-2/) Coding standard. To
fix issues with that easily we've added the ```php-cs-fixer``` to the
development-tools. So you can always correct issues by running
```./vendor/bin/php-cs-fixer```. That should take care of everything.

## Code of Conduct

This project is **open to everyone** as long as you follow
[the rules](CODE_OF_CONDUCT.md). They basically boil down to "behave yourself
and respect others!"
