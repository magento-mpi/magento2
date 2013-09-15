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
 * Form button element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

class Button extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * Additional html attributes
     *
     * @var array
     */
    protected $_htmlAttributes = array('data-mage-init');

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
        $this->setType('button');
        $this->setExtType('textfield');
    }

    /**
     * Html attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();
        return array_merge($attributes, $this->_htmlAttributes);
    }
}
