<?php
/**
 * ExportImport Controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Adminhtml_ExportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorizationModel;

    /**
     * Event manager model
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Saas_ImportExport_Model_Flag
     */
    protected $_flag;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Core_Model_Authorization $authorizationModel
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Saas_ImportExport_Model_FlagFactory $flagFactory
     * @param string $areaCode
     * @param array $invokeArgs
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Core_Model_Authorization $authorizationModel,
        Mage_Core_Model_Event_Manager $eventManager,
        Saas_ImportExport_Model_FlagFactory $flagFactory,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layoutFactory, $areaCode,
            $invokeArgs
        );
        $this->_authorizationModel = $authorizationModel;
        $this->_eventManager = $eventManager;
        $this->_flag = $flagFactory->create();
        $this->_flag->loadSelf();
    }

    /**
     * Custom constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Saas_ImportExport');
    }

    /**
     * Check access (in the ACL) for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorizationModel->isAllowed('Mage_ImportExport::export');
    }

    /**
     * Add task to queue for processing export
     *
     * @return Saas_ImportExport_Adminhtml_ExportController
     * @throws Mage_Core_Exception
     */
    public function exportAction()
    {
//        return $this->_eventManager->dispatch($this->_getEventName(), array('export_params' => $this->getRequest()->getParams()));

        if ($this->getRequest()->getPost(Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP)) {
            try {
                if ($this->_flag->isTaskAdded()) {
                    throw new Mage_Core_Exception($this->__('Export task has been already added to queue'));
                }
                $this->_flag->saveAsQueued();
                $this->_eventManager->dispatch($this->_getEventName(), array('export_params' => $this->getRequest()->getParams()));
                $this->_getSession()->addSuccess($this->__('Export task has been added to queue'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('No valid data sent'));
            }
        } else {
            $this->_getSession()->addError($this->__('No valid data sent'));
        }
        return $this->_redirect('*/*/index');
    }

    /**
     * Get event name depends on export entity
     *
     * @return string
     */
    protected function _getEventName()
    {
        $entity = $this->getRequest()->getParam('entity');
        if ($entity == 'customer_address') {
            $entity = 'customer';
        }
        return 'process_export_' . $entity;
    }
}
