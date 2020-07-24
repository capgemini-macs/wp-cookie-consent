<?php

/**
 * Loads the \Foo\Bar\Baz\Qux class from /src/Baz/Qux.php:
 * 
 * @param string $class The fully-qualified class name.
 * @see  https://www.php-fig.org/psr/psr-4/
 * @return void
 */
spl_autoload_register( function ( $class ) {

    // project-specific namespace prefix
    $prefix = "MACS\\Cookie_Consent\\";

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';

    // does the class use the namespace prefix?
    $len = strlen( $prefix );

    if ( strncmp( $prefix, $class, $len ) !== 0 ) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr( $class, $len );

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if ( file_exists( $file ) ) {
        //phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable -- standard autoloader
        require $file;
    }
} );