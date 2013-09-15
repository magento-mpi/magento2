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
 * Product form weight field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Helper\Form;

class Weight extends \Magento\Data\Form\Element\Text
{
    const VIRTUAL_FIELD_HTML_ID = 'weight_and_type_switcher';

    /**
     * Is virtual checkbox element
     *
     * @var \Magento\Data\Form\Element\Checkbox
     */
    protected $_virtual;

    /**
     * Catalog helper
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_helper;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Catalog\Helper\Product $helper
     * @param array $attributes
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Catalog\Helper\Product $helper,
        array $attributes = array()
    ) {
        $this->_helper = $helper;
        $this->_virtual = $factoryElement->create('checkbox');
        $this->_virtual->setId(self::VIRTUAL_FIELD_HTML_ID)->setName('is_virtual')
            ->setLabel($this->_helper->getTypeSwitcherControlLabel());
        $attributes['class'] =
            'validate-number validate-zero-or-greater validate-number-range number-range-0-99999999.9999';
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
    }

    /**
     * Add Is Virtual checkbox html to weight field
     *
     * @return string
     */
    public function getElementHtml()
    {
        if (!$this->getForm()->getDataObject()->getTypeInstance()->hasWeight()) {
            $this->_virtual->setChecked('checked');
        }
        return '<div class="fields-group-2"><div class="field"><div class="addon"><div class="control">'
            . parent::getElementHtml()
            . '<label class="addafter" for="'
            . $this->getHtmlId()
            . '"><strong>' . __('lbs') . '</strong></label>'
            . '</div></div></div><div class="field choice">'
            . $this->_virtual->getElementHtml() . $this->_virtual->getLabelHtml()
            . '</div></div>';
    }

    /**
     * Set form for both fields
     *
     * @param \Magento\Data\Form $form
     * @return \Magento\Data\Form
     */
    public function setForm($form)
    {
        $this->_virtual->setForm($form);
        return parent::setForm($form);
    }
}
