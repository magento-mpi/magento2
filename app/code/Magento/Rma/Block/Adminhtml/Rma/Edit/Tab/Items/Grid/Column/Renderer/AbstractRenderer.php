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
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer;

class AbstractRenderer
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
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
        $statusManager = \Mage::getSingleton('Magento\Rma\Model\Item\Status');
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
