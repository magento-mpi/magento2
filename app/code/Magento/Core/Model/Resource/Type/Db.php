<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Magento_Core_Model_Resource_Type_Db extends Magento_Core_Model_Resource_Type_Abstract 
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_entityClass = 'Magento_Core_Model_Resource_Entity_Table';
    }
}
