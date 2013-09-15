<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form checkbox element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

class Checkbox extends \Magento\Data\Form\Element\AbstractElement
{
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
        $attributes = array()
    ) {
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
        $this->setType('checkbox');
        $this->setExtType('checkbox');
    }

    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'checked', 'onclick', 'onchange', 'disabled', 'tabindex');
    }

    public function getElementHtml()
    {
        if ($checked = $this->getChecked()) {
            $this->setData('checked', true);
        }
        else {
            $this->unsetData('checked');
        }
        return parent::getElementHtml();
    }

    /**
     * Set check status of checkbox
     *
     * @param boolean $value
     * @return \Magento\Data\Form\Element\Checkbox
     */
    public function setIsChecked($value=false)
    {
        $this->setData('checked', $value);
        return $this;
    }

    /**
     * Return check status of checkbox
     *
     * @return boolean
     */
    public function getIsChecked() {
        return $this->getData('checked');
    }
}
