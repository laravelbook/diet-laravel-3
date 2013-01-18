diet-laravel-3
==============

**Laravel 3 on crash-diet!**

![](http://i.imgur.com/O8tej.jpg)

To improve performance of web applications, many developers utilize PHP op-code caching libraries such as APC, XCache and eAccelerator.

When op-code caching is enabled, you can replace the traditional `laravel.php` with a different Laravel bootstrap file named `laravel_boot.php` (which acts as a wrapper around `laravel_lite.php`) to further boost the performance of your application.

The file `laravel_lite.php` is the result of merging commonly used Laravel and Symfony class files with some extraneous classes, comments and code decorations stripped away. Therefore, using the highly condensed and optimized `laravel_lite.php` would greatly reduce the number of files being included and drastically improve PHP parsing speed.

## Getting started

Copy the two files `laravel_boot.php` and `laravel_lite.php` to your `laravel/` folder.

Open the `public/index.php` file. Find the line below:

```php
require path('sys').'laravel.php'; 
```

Replace the above line with this:

```php
require path('sys').'laravel_boot.php';
```

### What's removed

The diet version of Laravel does not contain Artisan and other command-line based features. These modules were deliberately stripped away to further optimize loading time of the web application. If you need to use the Artisan tool, switch back to regular `laravel.php` temporarily.

## Feedback

Rants, raves and bug reports are welcome.

Copyright Taylor Otwell [http://laravel.com/](http://laravel.com/)

Portions Copyright Max Ehsan [http://laravelbook.com/](http://laravelbook.com/)