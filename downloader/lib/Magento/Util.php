<?php
/**
 * Collection of various useful functions
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class \Magento\Util
{
    /**
     * Return PHP version without optional suffix
     * Scheme: major.minor.release
     * @return string
     */
    public function getTrimmedPhpVersion()
    {
        //old php versions less than 5.2.7 are not interesting for us. Not supported.
        if (!defined('PHP_MAJOR_VERSION')) {
            return PHP_VERSION;
        }

        return implode('.', array(PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION));
    }

}