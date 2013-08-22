<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Class describing db table resource entity
 *
 */
class Magento_Core_Model_Resource_Entity_Table extends Magento_Core_Model_Resource_Entity_Abstract
{
    /**
     * Get table
     *
     * @return String
     */
    function getTable()
    {
        return $this->getConfig('table');
    }
}
