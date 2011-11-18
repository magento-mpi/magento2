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
 * Convert zend cache adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Convert_Adapter_Zend_Cache extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{

    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Zend_Cache::factory($this->getVar('frontend', 'Core'), $this->getVar('backend', 'File'));
        }
        return $this->_resource;
    }

    public function load()
    {
        $this->setData($this->getResource()->load($this->getVar('id')));
        return $this;
    }

    public function save()
    {
        $this->getResource()->save($this->getData(), $this->getVar('id'));
        return $this;
    }

}
