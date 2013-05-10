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
     * @var Saas_ImportExport_Model_Flag
     */
    protected $_flag;

    /**
     * Event manager model
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @param Saas_ImportExport_Model_Export $export
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Saas_ImportExport_Model_FlagFactory $flagFactory
     */
    public function __construct(
        Saas_ImportExport_Model_Export $export,
        Mage_Core_Model_Event_Manager $eventManager,
        Saas_ImportExport_Model_FlagFactory $flagFactory
    ) {
        $this->_exportModel = $export;
        $this->_flag = $flagFactory->create();
        $this->_flag->loadSelf();
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
        if ($exportParams['page'] == 1) {
            $this->_flag->saveAsProcessing();
        }
        $this->_exportModel->setData($exportParams);
        $this->_exportModel->export();
        if ($this->_exportModel->getIsLast()) {
            $this->_flag->saveAsFinished();
        } else {
            $flagData = array(
                'message' => ceil($exportParams['page'] * 100 / $this->_exportModel->getCountPages()) . '%'
            );
            $this->_flag->setFlagData($flagData);
            $this->_flag->save();
            $exportParams['page']++;
            $this->_eventManager->dispatch($observer->getEvent()->getName(), array('export_params' => $exportParams));
        }
        return $this;
    }
}
