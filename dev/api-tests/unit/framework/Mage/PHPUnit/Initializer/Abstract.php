<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract initializer class.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_PHPUnit_Initializer_Abstract
{
    /**
     * Runs initialization process.
     */
    abstract public function run();

    /**
     * Rollback all changes after the test is ended (on tearDown)
     * Can be empty if nothing to rollback.
     */
    abstract public function reset();
}
