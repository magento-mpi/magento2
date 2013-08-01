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
 * Adminhtml block for fieldset of product custom options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options extends Mage_Catalog_Block_Product_View_Options
{
    /**
     * Get option html block
     *
     * @param Mage_Catalog_Model_Product_Option $option
     *
     * @return string
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        $type = $this->getGroupOfOption($option->getType());
        $renderer = $this->getChildBlock($type);
        $renderer->setSkipJsReloadPrice(1)
            ->setProduct($this->getProduct())
            ->setOption($option);

        return $this->getChildHtml($type, false);
    }
}
