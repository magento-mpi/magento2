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
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_element = \Mage::getModel('Magento\Data\Form\Element\Checkbox');
        parent::__construct($data);
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
