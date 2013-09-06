<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for contents
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Contents
    extends Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Abstract
{
    /**
     * Retrieve list of targets
     *
     * @return array
     */
    public function getMageTargets()
    {
        $targets = Mage::getModel('Magento_Connect_Model_Extension')->getLabelTargets();
        if (!is_array($targets)) {
            $targets = array();
        }
        return $targets;
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Contents');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Contents');
    }
}
