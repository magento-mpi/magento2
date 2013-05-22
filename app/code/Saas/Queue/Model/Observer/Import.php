<?php
/**
 * Saas queue import observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Model_Observer_Import extends Saas_Queue_Model_ObserverAbstract
{
    /**
     * Instance of Import model
     *
     * @var Mage_ImportExport_Model_Import
     */
    protected $_importModel;

    /**
     * Event manager model
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Import state helper
     *
     * @var Saas_ImportExport_Helper_Import_State
     */
    protected $_stateHelper;

    /**
     * @param Mage_ImportExport_Model_Import $import
     * @param Saas_ImportExport_Helper_Import_State $stateHelper
     * @param Mage_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Mage_ImportExport_Model_Import $import,
        Saas_ImportExport_Helper_Import_State $stateHelper,
        Mage_Core_Model_Event_Manager $eventManager
    ) {
        $this->_importModel = $import;
        $this->_stateHelper = $stateHelper;
        $this->_eventManager = $eventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function useInEmailNotification()
    {
        return false;
    }

    /**
     * Import entity
     *
     * @param  Varien_Event_Observer $observer
     * @return Saas_Queue_Model_Observer_Import
     */
    public function processImport(Varien_Event_Observer $observer)
    {
        $this->_stateHelper->saveTaskAsProcessing();
        $this->_importModel->importSource();
        $this->_stateHelper->saveTaskAsFinished();
        // refresh index after import products
        if ($observer->getEvent()->getName() == 'process_import_catalog_product') {
            $this->_eventManager->dispatch('application_process_refresh_catalog');
        }
        return $this;
    }
}
