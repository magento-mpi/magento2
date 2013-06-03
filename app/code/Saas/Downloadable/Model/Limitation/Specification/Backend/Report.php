<?php
/**
 * Functionality limitation checker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Downloadable_Model_Limitation_Specification_Backend_Report
    implements Saas_Saas_Model_Limitation_SpecificationInterface
{
    /**
     * Check is allowed functionality for the module
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function isSatisfiedBy(Mage_Core_Controller_Request_Http $request)
    {
        return $request->getControllerName() != 'report_product'
            || $request->getControllerModule() != 'Mage_Adminhtml'
            || !in_array($request->getActionName(), array('downloads', 'exportDownloadsCsv', 'exportDownloadsExcel'));
    }
}
