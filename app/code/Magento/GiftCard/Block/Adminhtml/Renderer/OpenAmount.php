<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * HTML select element block
 *
 * @category   Magento
 * @package    Magento_GiftCard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCard\Block\Adminhtml\Renderer;

class OpenAmount extends \Magento\Data\Form\Element\Select
{
    /**
     * @var \Magento\Data\Form\Element\Checkbox
     */
    protected $_element;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        array $attributes = array()
    ) {
        $this->_element = $factoryElement->create('checkbox');
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
    }

    /**
     * Set form to element
     *
     * @param $form
     * @return \Magento\Data\Form
     */
    public function setForm($form)
    {
        $this->_element->setForm($form);
        return parent::setForm($form);
    }

    /**
     * Return rendered field
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->_element->setId($this->getHtmlId())->setName($this->getData('name'))
            ->setChecked($this->getValue())->setValue(\Magento\GiftCard\Model\Giftcard::OPEN_AMOUNT_ENABLED);
        $hiddenField = '<input type="hidden" name="' . $this->getName()
            . '" value="' . \Magento\GiftCard\Model\Giftcard::OPEN_AMOUNT_DISABLED . '"/>';
        return $hiddenField . $this->_element->getElementHtml();
    }
}
