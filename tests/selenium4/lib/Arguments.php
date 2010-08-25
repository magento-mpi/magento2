<?php
/**
 * Command line arguments container
 *
 * @author Magento Inc.
 */
class Arguments {
    /**
     * Command line options
     *
     * @var array
     */
    public static $options = array();

    /**
     * Parse command line options
     */
    public static function init()
    {
        $args = $_SERVER['argv'];
        array_shift($args);
        self::$options = $args;
    }
}
