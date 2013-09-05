<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item Widget Form Textarea Element Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Item_Form_Element_Textarea extends Magento_Data_Form_Element_Textarea
{
    /**
     * Rma eav
     *
     * @var Magento_Rma_Helper_Eav
     */
    protected $_rmaEav = null;

    /**
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Rma_Helper_Eav $rmaEav
     * @param array $attributes
     */
    public function __construct(
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Rma_Helper_Eav $rmaEav,
        array $attributes = array()
    ) {
        $this->_rmaEav = $rmaEav;
        parent::__construct($factoryElement, $attributes);
    }

    /**
     * Return Form Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $additionalClasses = $this->_rmaEav
            ->getAdditionalTextElementClasses($this->getEntityAttribute());
        foreach ($additionalClasses as $additionalClass) {
            $this->addClass($additionalClass);
        }
        return parent::getElementHtml();
    }
}
