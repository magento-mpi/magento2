<?php
/**
 * Saas queue export observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Model_Observer_Export extends Saas_Queue_Model_ObserverAbstract
{
    /**
     * Instance of Export model
     *
     * @var Saas_ImportExport_Model_Export
     */
    protected $_exportModel;

    /**
     * Event manager model
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @param Saas_ImportExport_Model_Export $export
     * @param Mage_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Saas_ImportExport_Model_Export $export,
        Mage_Core_Model_Event_Manager $eventManager
    ) {
        $this->_exportModel = $export;
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
     * Export entity
     *
     * @param  Varien_Event_Observer $observer
     * @return Saas_Queue_Model_Observer_Export
     */
    public function processExport(Varien_Event_Observer $observer)
    {
        $exportParams = $observer->getEvent()->getExportParams();
        if (!isset($exportParams['page'])) {
            $exportParams['page'] = 1;
        }
        $this->_exportModel->export($exportParams);
        if (!$this->_exportModel->getIsFinished()) {
            $exportParams['page']++;
            $this->_eventManager->dispatch($observer->getEvent()->getName(), array('export_params' => $exportParams));
        }
        return $this;
    }
}
