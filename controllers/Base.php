<?php
namespace Controller;

use Slim\Container;

/**
 * Base controller will help you working with Slim more easily
 *
 */
class Base
{
    /**
     * Slim Container instance
     *
     * @var [type]
     */
    protected static $container;

    // --------------------------------------------------------------------

    /**
     * Constructor
     * store Slim Container instance in static property
     *
     * @param Container $container [description]
     */
    public function __construct(Container $container)
    {
        static::$container = $container;
    }

    // --------------------------------------------------------------------

    /**
     * defer all undefined properties to container
     *
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function __get($name)
    {
        return static::$container->get($name);
    }

    // --------------------------------------------------------------------

    /**
     * defer all method call to container (only those callable)
     * @param  [type] $name [description]
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    public function __call($name, $args)
    {
        $closure = static::$container->get($name);

        if (is_callable($closure)) {
            return call_user_func_array($closure, $args);
        } else {
            throw new \Exception('Call to undefined method '.get_class($this).'::'.$name);
        }
    }
}
