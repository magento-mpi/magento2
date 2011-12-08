<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Mage_Core_Model_Resource_Type_Db extends Mage_Core_Model_Resource_Type_Abstract 
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_entityClass = 'Mage_Core_Model_Resource_Entity_Table';
    }
}
