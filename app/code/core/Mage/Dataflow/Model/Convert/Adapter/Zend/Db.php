<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Convert zend db adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Convert_Adapter_Zend_Db extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{

    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Zend_Db::factory($this->getVar('adapter', 'Pdo_Mysql'), $this->getVars());
        }
        return $this->_resource;
    }

    public function load()
    {
        return $this;
    }

    public function save()
    {
        return $this;
    }

}
