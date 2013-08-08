<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config source reports event store filter
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Reports_Scope implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Scope filter
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'website', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Website')),
            array('value'=>'group', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Store')),
            array('value'=>'store', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Store View')),
        );
    }

}
