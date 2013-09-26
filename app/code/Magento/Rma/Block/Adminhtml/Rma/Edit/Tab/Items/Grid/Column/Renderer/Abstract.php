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
 * Grid column widget for rendering action grid cells depending on item status
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Abstract
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var Magento_Rma_Model_Item_Status
     */
    protected $_itemStatus;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Rma_Model_Item_Status $itemStatus
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Rma_Model_Item_Status $itemStatus,
        array $data = array()
    ) {
        $this->_itemStatus = $itemStatus;
        parent::__construct($context, $data);
    }

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
        $this->_itemStatus->setStatus($row->getStatus());
        $this->setStatusManager($this->_itemStatus);

        if ($this->_itemStatus->getAttributeIsEditable($this->getColumn()->getIndex())) {
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
