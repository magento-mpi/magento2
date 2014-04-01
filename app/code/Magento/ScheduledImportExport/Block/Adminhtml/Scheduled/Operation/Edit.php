<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation;

/**
 * Scheduled operation create/edit form container
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Import export data
     *
     * @var \Magento\ScheduledImportExport\Helper\Data
     */
    protected $_importExportData = null;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory
     */
    protected $_operationFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory $operationFactory
     * @param \Magento\ScheduledImportExport\Helper\Data $importExportData
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory $operationFactory,
        \Magento\ScheduledImportExport\Helper\Data $importExportData,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_operationFactory = $operationFactory;
        $this->_coreRegistry = $registry;
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
        $this->_blockGroup = 'Magento_ScheduledImportExport';
        $this->_mode = 'edit';
        $this->_controller = 'adminhtml_scheduled_operation';

        $operationId = (int)$this->getRequest()->getParam($this->_objectId);
        /** @var \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation */
        $operation = $this->_operationFactory->create();
        if ($operationId) {
            $operation->load($operationId);
        } else {
            $operation->setOperationType($this->getRequest()->getParam('type'))->setStatus(true);
        }
        $this->_coreRegistry->register('current_operation', $operation);

        parent::_construct();
    }

    /**
     * Prepare page layout.
     * Set form object to container.
     *
     * @throws \Magento\Model\Exception
     * @return $this
     */
    protected function _prepareLayout()
    {
        $operation = $this->_coreRegistry->registry('current_operation');
        $blockName = 'Magento\\ScheduledImportExport\\Block\\Adminhtml\\Scheduled\\Operation\\Edit\\Form\\' . ucfirst(
            $operation->getOperationType()
        );
        $formBlock = $this->getLayout()->createBlock($blockName);
        if ($formBlock) {
            $this->setChild('form', $formBlock);
        } else {
            throw new \Magento\Model\Exception(__('Please correct the scheduled operation type.'));
        }

        $this->_updateButton(
            'delete',
            'onclick',
            'deleteConfirm(\'' . $this->_importExportData->getConfirmationDeleteMessage(
                $operation->getOperationType()
            ) . '\', \'' . $this->getDeleteUrl() . '\')'
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
        return $this->getUrl(
            'adminhtml/*/delete',
            array(
                $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                'type' => $this->_coreRegistry->registry('current_operation')->getOperationType()
            )
        );
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
        return $this->_importExportData->getOperationHeaderText($operation->getOperationType(), $action);
    }
}
