<?php
/**
 * Collection of various useful functions
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Util
{
    /**
     * Return PHP version without optional suffix
     * Scheme: major.minor.release
     * @return string
     */
    public function getTrimmedPhpVersion()
    {
        return implode('.', array(PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION));
    }

}