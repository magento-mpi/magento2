<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config source reports event store filter
 *
 * @category   Mage
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Source_Reports_Scope implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Scope filter
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'website', 'label'=>Mage::helper('Magento_Backend_Helper_Data')->__('Website')),
            array('value'=>'group', 'label'=>Mage::helper('Magento_Backend_Helper_Data')->__('Store')),
            array('value'=>'store', 'label'=>Mage::helper('Magento_Backend_Helper_Data')->__('Store View')),
        );
    }

}
