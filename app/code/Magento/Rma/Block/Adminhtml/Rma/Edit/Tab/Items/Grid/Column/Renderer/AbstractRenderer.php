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
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid_Column_Renderer;

class AbstractRenderer
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Rma\Model\Item\Status
     */
    protected $_itemStatus;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Rma\Model\Item\Status $itemStatus
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Rma\Model\Item\Status $itemStatus,
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
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
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
     * @param \Magento\Object $row
     * @return string
     */
    protected function _getEditableView(\Magento\Object $row)
    {
        return parent::render($row);
    }

    /**
     * Render method when attribute is not editable
     *
     * Must be overwritten in child classes
     *
     * @param \Magento\Object $row
     * @return string
     */
    protected function _getNonEditableView(\Magento\Object $row)
    {
        return parent::render($row);
    }
}
