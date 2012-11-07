<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import Export Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method Community2_Mage_ImportExport_Helper helper(string $className)
 */
class Enterprise2_Mage_ImportExport_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Build full export URL
     *
     * @return string
     */
    public function _getExportFileUrl()
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->_getExportFileUrl();
    }

    /**
     * Generate URL for selected area
     *
     * @param string $uri
     * @param null|array $params
     *
     * @return string
     */
    public function _getUrl($uri, $params = null)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->_getUrl($uri, $params);
    }

    /**
     * Build full import URL
     *
     * @param bool $validate Specify step of Import - Data Validate or Import
     *                       true - Data Validate
     *                       false - Import step
     *
     * @return string
     */
    public function _getImportFileUrl($validate = true)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->_getImportFileUrl($validate);
    }

    /**
     * Return Form key
     *
     * @param string $pageHTML HTML response from the server
     *
     * @return string
     */
    public function _getFormKey($pageHTML)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->_getFormKey($pageHTML);
    }

    /**
     * Return Form key
     *
     * @param string $response HTML response from the server
     */
    public function _parseResponseMessages($response)
    {
        $this->helper('Community2/Mage/ImportExport/Helper')->_parseResponseMessages($response);
    }

    /**
     * Prepare import parameters array for uploadFile method and Export functionality
     *
     * @param array $parameters
     *
     * @return array
     */
    public function _prepareImportParameters($parameters = array())
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->_prepareImportParameters($parameters);
    }

    /**
     * Prepare export parameters array for getFile method and Export functionality
     *
     * @param array $parameters
     *
     * @return array
     */
    public function _prepareExportParameters($parameters = array())
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->_prepareExportParameters($parameters);
    }

    /**
     * Prepare skip attributes for getFile method and Export functionality
     *
     * @param array $parameters
     *
     * @return array
     */
    public function _prepareExportSkipAttributes($parameters = array())
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->_prepareExportSkipAttributes($parameters);
    }

    /**
     * Upload file to import area
     *
     * @param string $urlPage Url to the page for defining form key
     * @param string $importUrl Url to the Check Data
     * @param string $startUrl Url to the Import
     * @param array $parameters Submit form parameters
     * @param string|null $fileName Specific file name
     * @param bool $continueOnError Continue Import or not if error is occurred
     *
     * @return array
     */
    public function _uploadFile($urlPage, $importUrl, $startUrl, $parameters = array(), $fileName, $continueOnError = true)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')
            ->_uploadFile($urlPage, $importUrl, $startUrl, $parameters, $fileName, $continueOnError);
    }

    /**
     * Perform import with current selected options
     *
     * @param array $data Associative multidimensional array to be uploaded
     * @param string|null $fileName File name to be used for uploading
     * @param bool $continueOnError Continue Import or not if error is occurred
     *
     * @return array
     */
    public function import(array $data, $fileName = NULL, $continueOnError = true)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->import($data, $fileName, $continueOnError);
    }

    /**
     * Perform export with current selected options
     *
     * @return array
     */
    public function export()
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->export();
    }

    /**
     * Choose Import dialog options
     *
     * @param string $entityType Entity type to Import (Products/Customers/Customers Main File/
     * Customer Addresses/Customer Finances)
     * @param string $importBehavior Import behavior
     * @param string $fileName Import file name
     *
     * @return $this
     */
    public function chooseImportOptions($entityType, $importBehavior = Null, $fileName = Null)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')
            ->chooseImportOptions($entityType, $importBehavior, $fileName);
    }

    /**
     * Choose Export dialog options
     *
     * @param string $entityType Entity type to Export
     *              (Products/Customers Main File/Customer Addresses/Customer Finances)
     *
     * @return $this
     */
    public function chooseExportOptions($entityType)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->chooseExportOptions($entityType);
    }

    /**
     * Search customer/address/finance line in exported array
     * Returns line index
     *
     * @param string $fileType File type (master|address|finance)
     * @param array $needleData Main/Address/Finance line data
     * @param array $fileLines Array from csv file
     *
     * @return int|null
     */
    public function lookForEntity($fileType, $needleData, $fileLines)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->lookForEntity($fileType, $needleData, $fileLines);
    }

    /**
     * Converts customer data to format comparable with csv data
     *
     * @param $rawData
     *
     * @return array
     */
    public function prepareMasterData($rawData)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->prepareMasterData($rawData);
    }

    /**
     * * Converts address data to format comparable with csv data
     *
     * @param $rawData
     *
     * @return array
     */
    public function prepareAddressData($rawData)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->prepareAddressData($rawData);
    }

    /**
     * Converts finance data to format comparable with csv data
     *
     * @param $rawData
     *
     * @return array
     */
    public function prepareFinanceData($rawData)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->prepareFinanceData($rawData);
    }

    /**
     * Apply customer attributes filter
     *
     * @param array $fieldParams
     *            example:
     *             array('attribute_label' => 'text_label', 'attribute_code' => 'text_code')))
     */
    public function customerFilterAttributes(array $fieldParams)
    {
        $this->helper('Community2/Mage/ImportExport/Helper')->customerFilterAttributes($fieldParams);
    }

    /**
     * Search attribute in grid and return attribute xPath
     *
     * @param array $fieldParams
     * @param string $fieldset
     *
     * @return array|null
     */
    public function customerSearchAttributes(array $fieldParams, $fieldset)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')->customerSearchAttributes($fieldParams, $fieldset);
    }

    /**
     * Mark attribute as skipped
     *
     * @param array $fieldParams
     * @param string $fieldset
     * @param bool $skip
     *
     * @return bool
     */
    public function customerSkipAttribute(array $fieldParams, $fieldset, $skip = true)
    {
        return $this->helper('Community2/Mage/ImportExport/Helper')
            ->customerSkipAttribute($fieldParams, $fieldset, $skip);
    }

    /**
     * Get list of Customer Entity Types specific for Magento versions
     *
     * @return array
     */
    public function getCustomerEntityType()
    {
        return array('Customers Main File', 'Customer Addresses', 'Customer Finances');
    }

    /**
     * Fill filter form
     *
     * @param array $data array(attribute_code => attribute_value)
     *
     * @throws Exception
     */
    public function setFilter($data)
    {
        $this->helper('Community2/Mage/ImportExport/Helper')->setFilter($data);
    }
}
