<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Backend\Model\Config\Source;

class Website implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();
            foreach (\Mage::app()->getWebsites() as $website) {
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
