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
 * Tax Rate Titles Fieldset
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Tax_Rate_Title_Fieldset extends Magento_Data_Form_Element_Fieldset
{
    /**
     * @var Magento_Adminhtml_Block_Tax_Rate_Title
     */
    protected $_title;

    /**
     * @param Magento_Adminhtml_Block_Tax_Rate_Title $title
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        Magento_Adminhtml_Block_Tax_Rate_Title $title,
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        $this->_title = $title;
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
    }

    public function getBasicChildrenHtml()
    {
        return $this->_title->toHtml();
    }
}
