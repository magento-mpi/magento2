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
class Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit
    extends Magento_Backend_Block_Widget_Form_Container
{
    /**
     * Import export data
     *
     * @var Magento_ScheduledImportExport_Helper_Data
     */
    protected $_importExportData = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_ScheduledImportExport_Helper_Data $importExportData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_ScheduledImportExport_Helper_Data $importExportData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_importExportData = $importExportData;
        parent::__construct($coreData, $context, $data);
    }

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
        $operation = Mage::getModel('Magento_ScheduledImportExport_Model_Scheduled_Operation');
        if ($operationId) {
            $operation->load($operationId);
        } else {
            $operation->setOperationType($this->getRequest()->getParam('type'))
                ->setStatus(true);
        }
        $this->_coreRegistry->register('current_operation', $operation);

        parent::_construct();
    }

    /**
     * Prepare page layout.
     * Set form object to container.
     *
     * @return Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit
     */
    protected function _prepareLayout()
    {
        $operation = $this->_coreRegistry->registry('current_operation');
        $blockName = 'Magento_ScheduledImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_'
            . ucfirst($operation->getOperationType());
        $formBlock = $this->getLayout()
            ->createBlock($blockName);
        if ($formBlock) {
            $this->setChild('form', $formBlock);
        } else {
            throw new Magento_Core_Exception(__('Please correct the scheduled operation type.'));
        }

        $this->_updateButton('delete', 'onclick', 'deleteConfirm(\''
            . $this->_importExportData->getConfirmationDeleteMessage($operation->getOperationType())
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
            'type' => $this->_coreRegistry->registry('current_operation')->getOperationType()
        ));
    }

    /**
     * Get page header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $operation = $this->_coreRegistry->registry('current_operation');
        if ($operation->getId()) {
            $action = 'edit';
        } else {
            $action = 'new';
        }
        return $this->_importExportData->getOperationHeaderText(
            $operation->getOperationType(),
            $action
        );
    }
}
