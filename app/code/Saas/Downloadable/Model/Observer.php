<?php
/**
 * Observer for the Saas_Downloadable module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Downloadable_Model_Observer
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    private $_request;

    /**
     * @var Saas_Saas_Helper_Data
     */
    private $_saasHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Saas_Saas_Helper_Data $saasHelper
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Saas_Saas_Helper_Data $saasHelper
    ) {
        $this->_request = $request;
        $this->_saasHelper = $saasHelper;
    }

    /**
     * Disable report product downloads
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Downloadable_Model_Observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function disableReportProductDownloads(Varien_Event_Observer $observer)
    {
        if ($this->_isNeededToDisable()) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
        return $this;
    }

    /**
     * @return bool
     */
    protected function _isNeededToDisable()
    {
        return $this->_request->getControllerName() == 'report_product'
            && $this->_request->getControllerModule() == 'Mage_Adminhtml'
            && in_array($this->_request->getActionName(),
                array('downloads', 'exportDownloadsCsv', 'exportDownloadsExcel'));
    }
}
