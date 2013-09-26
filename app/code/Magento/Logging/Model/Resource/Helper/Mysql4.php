<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource helper class
 */
class Magento_Logging_Model_Resource_Helper_Mysql4 extends Magento_Core_Model_Resource_Helper_Mysql4
{
    /**
     * Construct
     *
     * @param string $modulePrefix
     */
    public function __construct($modulePrefix = 'Logging')
    {
        parent::__construct($modulePrefix);
    }
}
