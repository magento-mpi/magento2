<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rendera attribute block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Attribute
    extends Mage_Backend_Block_Template
{
    /**
     * Render block
     *
     * @param array $arguments
     * @return string
     */
    public function render(array $arguments)
    {
        $this->assign($arguments);
        return $this->toHtml();
    }
}
