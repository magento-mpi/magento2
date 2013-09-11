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
 * Bundle selection renderer
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

class Selection extends \Magento\Adminhtml\Block\Widget
{
    protected $_template = 'product/edit/bundle/option/selection.phtml';

    /**
     * Initialize bundle option selection block
     */
    protected function _construct()
    {

        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }

    /**
     * Return field id
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'bundle_selection';
    }

    /**
     * Return field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'bundle_selections';
    }

    /**
     * Prepare block layout
     *
     * @return \Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Selection
     */
    protected function _prepareLayout()
    {
        $this->addChild('selection_delete_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label' => __('Delete'),
            'class' => 'delete icon-btn',
            'on_click' => 'bSelection.remove(event)'
        ));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve delete button html
     *
     * @return string
     */
    public function getSelectionDeleteButtonHtml()
    {
        return $this->getChildHtml('selection_delete_button');
    }

    /**
     * Retrieve price type select html
     *
     * @return string
     */
    public function getPriceTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Html\Select')
            ->setData(array(
                'id'    => $this->getFieldId() . '_{{index}}_price_type',
                'class' => 'select select-product-option-type required-option-select'
            ))
            ->setName($this->getFieldName() . '[{{parentIndex}}][{{index}}][selection_price_type]')
            ->setOptions(\Mage::getSingleton('Magento\Bundle\Model\Source\Option\Selection\Price\Type')->toOptionArray());
        if ($this->getCanEditPrice() === false) {
            $select->setExtraParams('disabled="disabled"');
        }
        return $select->getHtml();
    }

    /**
     * Retrieve qty type select html
     *
     * @return string
     */
    public function getQtyTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Html\Select')
            ->setData(array(
                'id' => $this->getFieldId().'_{{index}}_can_change_qty',
                'class' => 'select'
            ))
            ->setName($this->getFieldName().'[{{parentIndex}}][{{index}}][selection_can_change_qty]')
            ->setOptions(\Mage::getSingleton('Magento\Backend\Model\Config\Source\Yesno')->toOptionArray());

        return $select->getHtml();
    }

    /**
     * Return search url
     *
     * @return string
     */
    public function getSelectionSearchUrl()
    {
        return $this->getUrl('*/bundle_selection/grid');
    }

    /**
     * Check if used website scope price
     *
     * @return string
     */
    public function isUsedWebsitePrice()
    {
        return !\Mage::helper('Magento\Catalog\Helper\Data')->isPriceGlobal() && \Mage::registry('product')->getStoreId();
    }

    /**
     * Retrieve price scope checkbox html
     *
     * @return string
     */
    public function getCheckboxScopeHtml()
    {
        $checkboxHtml = '';
        if ($this->isUsedWebsitePrice()) {
            $id = $this->getFieldId() . '_{{index}}_price_scope';
            $name = $this->getFieldName() . '[{{parentIndex}}][{{index}}][default_price_scope]';
            $class = 'bundle-option-price-scope-checkbox';
            $label = __('Use Default Value');
            $disabled = ($this->getCanEditPrice() === false) ? ' disabled="disabled"' : '';
            $checkboxHtml = '<input type="checkbox" id="' . $id . '" class="' . $class . '" name="' . $name
                . '"' . $disabled . ' value="1" />';
            $checkboxHtml .= '<label class="normal" for="' . $id . '">' . $label . '</label>';
        }
        return $checkboxHtml;
    }
}
