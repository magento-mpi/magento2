<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type;

/**
 * Bundle option dropdown type renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Select extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Select
{
    /**
     * @var string
     */
    protected $_template = 'product/composite/fieldset/options/type/select.phtml';

    /**
     * @param  string $elementId
     * @param  string $containerId
     * @return string
     */
    public function setValidationContainer($elementId, $containerId)
    {
        return '<script type="text/javascript">
            $(\'' .
            $elementId .
            '\').advaiceContainer = \'' .
            $containerId .
            '\';
            </script>';
    }
}
