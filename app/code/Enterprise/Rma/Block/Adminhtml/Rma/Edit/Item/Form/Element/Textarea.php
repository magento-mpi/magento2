<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item Widget Form Textarea Element Block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Item_Form_Element_Textarea extends Magento_Data_Form_Element_Textarea
{
    /**
     * Rma eav
     *
     * @var Enterprise_Rma_Helper_Eav
     */
    protected $_rmaEav = null;

    /**
     * @param Enterprise_Rma_Helper_Eav $rmaEav
     */
    public function __construct(
        Enterprise_Rma_Helper_Eav $rmaEav
    ) {
        $this->_rmaEav = $rmaEav;
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
