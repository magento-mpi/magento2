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
     * Constructor for our block with options
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addOptionRenderer(
            'default',
            'Magento_Catalog_Block_Product_View_Options_Type_Default',
            'catalog/product/composite/fieldset/options/type/default.phtml'
        );
    }

    /**
     * Get option html block
     *
     * @param Magento_Catalog_Model_Product_Option $option
     *
     * @return string
     */
    public function getOptionHtml(Magento_Catalog_Model_Product_Option $option)
    {
        $renderer = $this->getOptionRender(
            $this->getGroupOfOption($option->getType())
        );
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate($renderer['template'])
                ->setSkipJsReloadPrice(1);
        }
        return $renderer['renderer']
            ->setProduct($this->getProduct())
            ->setOption($option)
            ->toHtml();
    }
}
