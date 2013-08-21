<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Bundle option multi select type renderer
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Options_Type_Multi
    extends Magento_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Multi
{
    protected $_template = 'product/composite/fieldset/options/type/multi.phtml';

    /**
     * @param  string $elementId
     * @param  string $containerId
     * @return string
     */
    public function setValidationContainer($elementId, $containerId)
    {
        return '<script type="text/javascript">
            $(\'' . $elementId . '\').advaiceContainer = \'' . $containerId . '\';
            </script>';
    }
}
