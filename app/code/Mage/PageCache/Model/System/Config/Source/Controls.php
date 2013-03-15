<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache system config source model
 *
 * @category   Mage
 * @package    Mage_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Model_System_Config_Source_Controls
{
    /**
     * Return array of external cache controls for using as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach (Mage::helper('Mage_PageCache_Helper_Data')->getCacheControls() as $code => $type) {
            $options[] = array(
                'value' => $code,
                'label' => $type['label']
            );
        }
        return $options;
    }
}
