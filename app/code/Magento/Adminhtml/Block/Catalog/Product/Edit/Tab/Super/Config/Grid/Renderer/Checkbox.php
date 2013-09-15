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
 * Adminhtml catalog super product link grid checkbox renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config\Grid\Renderer;

class Checkbox extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Checkbox
{
    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Context $context,
        Magento_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context, $converter, $data);
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $result = parent::render($row);
        return $result.'<input type="hidden" class="value-json" value="'.htmlspecialchars($this->getAttributesJson($row)).'" />';
    }

    public function getAttributesJson(\Magento\Object $row)
    {
        if(!$this->getColumn()->getAttributes()) {
            return '[]';
        }

        $result = array();
        foreach($this->getColumn()->getAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            if($productAttribute->getSourceModel()) {
                $label = $productAttribute->getSource()->getOptionText($row->getData($productAttribute->getAttributeCode()));
            } else {
                $label = $row->getData($productAttribute->getAttributeCode());
            }
            $item = array();
            $item['label']        = $label;
            $item['attribute_id'] = $productAttribute->getId();
            $item['value_index']  = $row->getData($productAttribute->getAttributeCode());
            $result[] = $item;
        }

        return $this->_coreData->jsonEncode($result);
    }
}// Class \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config\Grid\Renderer\Checkbox END
