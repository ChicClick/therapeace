<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit8a27189d1ca242afe4b5a11d57c14adc
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit8a27189d1ca242afe4b5a11d57c14adc', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit8a27189d1ca242afe4b5a11d57c14adc', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit8a27189d1ca242afe4b5a11d57c14adc::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}