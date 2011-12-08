<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml airmail queue grid block action item renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Queue_Grid_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Render grid row
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = array(
            array(
                'caption'   => $this->__('Preview'),
                'url'       => $this->getUrl('*/*/previewQueue', array('id' => $row->getId())),
                'popup'     => true,
            ),
        );

        if ($row->getStatus() == Mage_XmlConnect_Model_Queue::STATUS_IN_QUEUE) {
            $actions[] = array(
                'caption'   => $this->__('Edit'),
                'url'       => $this->getUrl('*/*/editQueue', array('id' => $row->getId())),
            );
            $actions[] = array(
                'caption'   => $this->__('Cancel'),
                'url'       => $this->getUrl('*/*/cancelQueue', array('id' => $row->getId())),
                'confirm'   => $this->__('Are you sure you whant to cancel a message?')
            );
        }

        $actions[] = array(
            'caption'   => $this->__('Delete'),
            'url'       => $this->getUrl('*/*/deleteQueue', array('id' => $row->getId())),
            'confirm'   => $this->__('Are you sure you whant to delete a message?')
        );

        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }
}
