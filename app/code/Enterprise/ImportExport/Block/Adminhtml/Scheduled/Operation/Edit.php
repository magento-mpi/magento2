<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scheduled operation create/edit form container
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit
    extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Import export data
     *
     * @var Enterprise_ImportExport_Helper_Data
     */
    protected $_importExportData = null;

    /**
     * @param Enterprise_ImportExport_Helper_Data $importExportData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_ImportExport_Helper_Data $importExportData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_importExportData = $importExportData;
        parent::__construct($context, $data);
    }

    /**
     * Initialize operation form container.
     * Create operation instance from database and set it to register.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Enterprise_ImportExport';
        $this->_mode = 'edit';
        $this->_controller = 'adminhtml_scheduled_operation';

        $operationId = (int)$this->getRequest()->getParam($this->_objectId);
        $operation = Mage::getModel('Enterprise_ImportExport_Model_Scheduled_Operation');
        if ($operationId) {
            $operation->load($operationId);
        } else {
            $operation->setOperationType($this->getRequest()->getParam('type'))
                ->setStatus(true);
        }
        Mage::register('current_operation', $operation);

        parent::_construct();
    }

    /**
     * Prepare page layout.
     * Set form object to container.
     *
     * @return Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit
     */
    protected function _prepareLayout()
    {
        $operation = Mage::registry('current_operation');
        $blockName = 'Enterprise_ImportExport_Block_Adminhtml_Scheduled_Operation_Edit_Form_'
            . ucfirst($operation->getOperationType());
        $formBlock = $this->getLayout()
            ->createBlock($blockName);
        if ($formBlock) {
            $this->setChild('form', $formBlock);
        } else {
            Mage::throwException(__('Please correct the scheduled operation type.'));
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
            'type' => Mage::registry('current_operation')->getOperationType()
        ));
    }

    /**
     * Get page header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $operation = Mage::registry('current_operation');
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
