<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scheduled operation create/edit form container
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation;

class Edit
    extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Initialize operation form container.
     * Create operation instance from database and set it to register.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_ScheduledImportExport';
        $this->_mode = 'edit';
        $this->_controller = 'adminhtml_scheduled_operation';

        $operationId = (int)$this->getRequest()->getParam($this->_objectId);
        $operation = \Mage::getModel('\Magento\ScheduledImportExport\Model\Scheduled\Operation');
        if ($operationId) {
            $operation->load($operationId);
        } else {
            $operation->setOperationType($this->getRequest()->getParam('type'))
                ->setStatus(true);
        }
        \Mage::register('current_operation', $operation);

        parent::_construct();
    }

    /**
     * Prepare page layout.
     * Set form object to container.
     *
     * @return \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit
     */
    protected function _prepareLayout()
    {
        $operation = \Mage::registry('current_operation');
        $blockName = 'Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_'
            . ucfirst($operation->getOperationType());
        $formBlock = $this->getLayout()
            ->createBlock($blockName);
        if ($formBlock) {
            $this->setChild('form', $formBlock);
        } else {
            \Mage::throwException(__('Please correct the scheduled operation type.'));
        }

        $this->_updateButton('delete', 'onclick', 'deleteConfirm(\''
            . \Mage::helper('Magento\ScheduledImportExport\Helper\Data')->getConfirmationDeleteMessage($operation->getOperationType())
            .'\', \'' . $this->getDeleteUrl() . '\')'
        );

        return $this;
    }

    /**
     * Get operation delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array(
            $this->_objectId => $this->getRequest()->getParam($this->_objectId),
            'type' => \Mage::registry('current_operation')->getOperationType()
        ));
    }

    /**
     * Get page header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $operation = \Mage::registry('current_operation');
        if ($operation->getId()) {
            $action = 'edit';
        } else {
            $action = 'new';
        }
        return \Mage::helper('Magento\ScheduledImportExport\Helper\Data')->getOperationHeaderText(
            $operation->getOperationType(),
            $action
        );
    }
}
