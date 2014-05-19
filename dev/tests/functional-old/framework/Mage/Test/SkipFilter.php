<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for class to allow filter tests
 */
interface Mage_Test_SkipFilter
{
    /**
     * Filter test by name
     *
     * @abstract
     * @param string $name
     * @return bool
     */
    public function filter($name);
}
