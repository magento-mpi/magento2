<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Website implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();
            foreach (Mage::app()->getWebsites() as $website) {
                $id = $website->getId();
                $name = $website->getName();
                if ($id!=0) {
                    $this->_options[] = array('value'=>$id, 'label'=>$name);
                }
            }
        }
        return $this->_options;
    }
}
