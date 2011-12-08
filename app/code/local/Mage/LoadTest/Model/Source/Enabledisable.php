<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * LoadTest Enable/Disable source model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_LoadTest_Model_Source_Enabledisable
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('Mage_LoadTest_Helper_Data')->__('Enable')),
            array('value'=>0, 'label'=>Mage::helper('Mage_LoadTest_Helper_Data')->__('Disable')),
        );
    }
}