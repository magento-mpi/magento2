<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tool block to add new tab for cms page edit tab control
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Adminhtml_Cms_Page_Edit_Enable extends Mage_Adminhtml_Block_Template
{
    /**
     * Utility method to call method of specified block
     * in case google optimizer enabled for cms in the system.
     * Uses as parameters block name, method name and params for method.
     *
     * @param string $name
     * @param string $method
     * @param array $params
     * @return Mage_GoogleOptimizer_Block_Adminhtml_Cms_Page_Edit_Enable
     */
    public function ifGoogleOptimizerEnabled($name, $method, $params = array())
    {
        if (Mage::helper('Mage_GoogleOptimizer_Helper_Data')->isOptimizerActiveForCms()) {
            $block = $this->getLayout()->getBlock($name);
            if ($block) {
                call_user_func_array(array($block, $method), $params);
            }
        }

        return $this;
    }

    /**
     * in case google optimizer enabled for cms in the system.
     * Uses as parameters container name, block name, type and attributes
     *
     * @param string $container
     * @param string $name
     * @param string $type
     * @param array $attributes
     * @return Mage_GoogleOptimizer_Block_Adminhtml_Cms_Page_Edit_Enable
     */
    public function ifGoogleOptimizerEnabledAppend($container, $name, $type, $attributes = array())
    {
        if (Mage::helper('Mage_GoogleOptimizer_Helper_Data')->isOptimizerActiveForCms()) {
            $containerBlock = $this->getLayout()->getBlock($container);
            if ($containerBlock) {
                $block = $this->getLayout()->createBlock($type, $name, array('data' => $attributes));
                $containerBlock->append($block);
            }
        }

        return $this;
    }
}
