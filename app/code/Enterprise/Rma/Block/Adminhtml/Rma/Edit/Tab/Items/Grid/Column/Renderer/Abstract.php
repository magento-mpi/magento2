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
 * Grid column widget for rendering action grid cells depending on item status
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Abstract
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders column
     *
     * Render column depending on row status value, which define whether cell is editable
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        $statusManager = Mage::getSingleton('Enterprise_Rma_Model_Item_Status');
        $statusManager->setStatus($row->getStatus());
        $this->setStatusManager($statusManager);

        if ($statusManager->getAttributeIsEditable($this->getColumn()->getIndex())) {
            return $this->_getEditableView($row);
        } else {
            return $this->_getNonEditableView($row);
        }
    }

    /**
     * Render method when attribute is editable
     *
     * Must be overwritten in child classes
     *
     * @param Magento_Object $row
     * @return string
     */
    protected function _getEditableView(Magento_Object $row)
    {
        return parent::render($row);
    }

    /**
     * Render method when attribute is not editable
     *
     * Must be overwritten in child classes
     *
     * @param Magento_Object $row
     * @return string
     */
    protected function _getNonEditableView(Magento_Object $row)
    {
        return parent::render($row);
    }
}
