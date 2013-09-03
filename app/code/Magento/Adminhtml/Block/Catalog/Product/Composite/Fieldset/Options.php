<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml block for fieldset of product custom options
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options extends Magento_Catalog_Block_Product_View_Options
{
    /**
     * Get option html block
     *
     * @param Magento_Catalog_Model_Product_Option $option
     *
     * @return string
     */
    public function getOptionHtml(Magento_Catalog_Model_Product_Option $option)
    {
        $type = $this->getGroupOfOption($option->getType());
        $renderer = $this->getChildBlock($type);
        $renderer->setSkipJsReloadPrice(1)
            ->setProduct($this->getProduct())
            ->setOption($option);

        return $this->getChildHtml($type, false);
    }
}
