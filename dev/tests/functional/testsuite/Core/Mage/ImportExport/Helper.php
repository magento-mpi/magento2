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
 */
class Core_Mage_ImportExport_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Build full export URL
     *
     * @return string
     */
    public function _getExportFileUrl()
    {
        $path = 'entity/' . $this->getControlAttribute('dropdown', 'entity_type', 'selectedValue');
        $path .= '/file_format/' . $this->getControlAttribute('dropdown', 'file_format', 'selectedValue');
        return $path;
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
        return ($validate ? 'validate/?form_key=' : 'start/?form_key=');
    }

    /**
     * Get file from admin area
     * Suitable for export testing
     *
     * @param string $urlPage Url to the page for defining form key
     * @param string $url Url to the file or submit form
     * @param array $parameters Submit form parameters
     *
     * @throws RuntimeException
     * @return string
     */
    public function _getFile($urlPage, $url, $parameters = array())
    {
        //Get form key from the page
        $formKey = $this->getElement("//input[@name='form_key' and @type='hidden']")->value();
        if (!$formKey) {
            $this->fail('Form Key was not defined. Can not continue Import/Export.');
        }
        //Prepare export request
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=' . $this->cookie()->get('PHPSESSID'));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 120);
        //Convert parameters to string
        $fieldsString = '';
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $attrID) {
                    $fieldsString .= $key . '=' . urlencode($attrID) . '&';
                }
            } else {
                $fieldsString .= $key . '=' . urlencode($value) . '&';
            }
        }
        rtrim($fieldsString, '&');
        $fieldsString = "form_key={$formKey}&frontend_label=&" . $fieldsString;
        //Put parameters
        curl_setopt($curl, CURLOPT_POST, count($fieldsString));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fieldsString);
        //Request export
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            $error = 'Error connection[' . curl_errno($curl) . '] to ' . $url . ': ' . curl_error($curl);
            throw new RuntimeException($error);
        }
        curl_close($curl);
        //Return response
        return $data;
    }

    /**
     * Return Form key
     *
     * @param string $response HTML response from the server
     * @return void
     */
    public function _parseResponseMessages($response)
    {
        //Check fatal error
        if (preg_match('/Fatal error/i', $response, $result) == true || $response == '') {
            $this->addMessage('error', $response);
            return;
        }
        //parse response
        preg_match('/{"import_validation_messages":(".*")}/i', $response, $result);
        if (!isset($result[1])) {
            preg_match('/{"import_validation_container_header":(".*")}/i', $response, $result);
            if (!isset($result[1])) {
                $this->markTestIncomplete('MAGETWO-3858');
            }
            preg_match('/"import_validation_messages":"(.*)"/i', $result[1], $result);
        }
        $result = stripcslashes($result[1]);
        $dom = new DOMDocument;
        $dom->loadHTML($result);
        $domXPath = new DOMXPath($dom);
        $filtered = $domXPath->query("//ul[@class='messages']/li[@class]");
        /** @var DOMElement $message */
        foreach ($filtered as $message) {
            //get message type: notice-msg, success-msg, error-msg
            $messageType = $message->getAttribute('class');
            //get message
            $filteredMessages = $domXPath->query("//ul[@class='messages']/li[@class='{$messageType}']/ul/li/span");
            //get text
            foreach ($filteredMessages as $filteredMessage) {
                $messageText = $filteredMessage->nodeValue;
                switch ($messageType) {
                    case 'notice-msg':
                        $this->addMessage('validation', $messageText);
                        break;
                    case 'success-msg':
                        $this->addMessage('success', $messageText);
                        break;
                    case 'error-msg':
                        $this->addMessage('error', $messageText);
                        break;
                    default:
                        $this->addMessage('error', 'Unexpected message: ' . $messageText);
                        break;
                }
            }
        }
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
        $parameters['entity'] = $this->getControlAttribute('dropdown', 'entity_type', 'selectedValue');
        $parameters['behavior'] = $this->getControlAttribute('dropdown', 'import_behavior', 'selectedValue');
        return $parameters;
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
        $elementTypes = array('input', 'select');
        foreach ($elementTypes as $elementType) {
            $tablePath = "css=table#export_filter_grid_table>tbody>tr>td.last>{$elementType}[name*='export_filter']";
            $elements = $this->getElements($tablePath);
            /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
            foreach ($elements as $element) {
                $attValue = '';
                $attName = $element->attribute('name');
                switch ($elementType) {
                    case 'input':
                        $attValue = $element->attribute('value');
                        break;
                    case 'select':
                        $attValue = $this->select($element)->selectedLabel();
                        break;
                }
                $parameters[trim($attName)] = $attValue;
            }
        }
        return $parameters;
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
        //Collect skip attributes
        $tablePath = "css=table#export_filter_grid_table>tbody>tr>td>input[name='skip_attr[]']";
        $skipAttributes = array();
        $elements = $this->getElements($tablePath);
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
        foreach ($elements as $element) {
            if ($element->selected()) {
                $skipAttributes = $element->attribute('value');
            }
        }
        if (!empty($skipAttributes)) {
            $parameters['skip_attr[]'] = $skipAttributes;
        }
        return $parameters;
    }

    /**
     * Upload file to import area
     *
     * @param string $urlPage Url to the page for defining form key
     * @param string $importUrl Url to the Check Data
     * @param string $startUrl Url to the Import
     * @param array $params Submit form parameters
     * @param string|null $fileName Specific file name
     * @param bool $continueOnError Continue Import or not if error is occurred
     *
     * @throws RuntimeException
     * @return array
     */
    public function _uploadFile($urlPage, $importUrl, $startUrl, $params = array(), $fileName, $continueOnError = true)
    {
        //Get form key from the page
        $formKey = $this->getElement("//input[@name='form_key' and @type='hidden']")->value();
        if (!$formKey) {
            $this->fail('Form Key was not defined. Can not continue Import/Export.');
        }
        //Make tmp file
        $tempFile = $this->getConfigHelper()->getLogDir() . DIRECTORY_SEPARATOR
            . (is_null($fileName) ? 'customer_' . date('Ymd_His') . '.csv' : $fileName);
        $handle = fopen($tempFile, 'a+');
        fputs($handle, $params['import_file']);
        fflush($handle);
        fclose($handle);
        //Add request parameters
        $params['import_file'] = "@" . $tempFile;
        $params['form_key'] = $formKey;
        //Prepare Check Data request
        $curl = curl_init($importUrl . $formKey);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=' . $this->cookie()->get('PHPSESSID'));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 120);
        curl_setopt($curl, CURLOPT_POST, count($params));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        //Request Check Data
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            $error = 'Error connection[' . curl_errno($curl) . '] to '
                . $importUrl . $formKey . ': ' . curl_error($curl);
            throw new RuntimeException($error);
        }
        //Parse response messages
        $this->clearMessages();
        $this->_parseResponseMessages($data);
        //Save Check Data messages to validation array
        $importMessages['validation'] = $this->getParsedMessages();
        //verify validation message
        $continueImport = false;
        $importErrorOccurred = false;
        if (isset($importMessages['validation']['validation'])) {
            foreach ($importMessages['validation']['validation'] as $validationMessage) {
                $pattern = '/Checked rows: (\d+), checked entities: (\d+), invalid rows: (\d+), total errors: (\d+)/i';
                if (preg_match($pattern, $validationMessage, $result)) {
                    //compare checked and invalid rows
                    $continueImport = intval($result[1]) > intval($result[3]);
                    $importErrorOccurred = intval($result[1]) <> intval($result[3]);
                }
            }
        }
        //Perform Import if Check Data is passed
        if ($continueImport) {
            if (!$importErrorOccurred || ($importErrorOccurred && $continueOnError)) {
                //Prepare request Import Data
                $params['import_file'] = "type=application/octet-stream";
                //Request Import data
                curl_setopt($curl, CURLOPT_URL, $startUrl . $formKey);
                curl_setopt($curl, CURLOPT_POST, count($params));
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                $data = curl_exec($curl);
                if (curl_errno($curl)) {
                    $error = 'Error connection[' . curl_errno($curl) . '] to '
                        . $startUrl . $formKey . ': ' . curl_error($curl);
                    throw new RuntimeException($error);
                }
                //Parse response messages
                $this->clearMessages();
                $this->_parseResponseMessages($data);
                //Save Import messages to import array
                $importMessages['import'] = $this->getParsedMessages();
            }
        }
        //Close curl connection
        curl_close($curl);
        //Clear page messages
        $this->clearMessages();
        //Delete temp file
        unlink($tempFile);
        //Return messages
        return $importMessages;
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
    public function import(array $data, $fileName = null, $continueOnError = true)
    {
        //Get export page full Url
        $pageUrl = $this->url();
        //Get export file full url
        $importUrl = $pageUrl . $this->_getImportFileUrl();
        $startUrl = $pageUrl . $this->_getImportFileUrl(false);
        //Convert array to CSV
        $csv = $this->csvHelper()->arrayToCsv($data);
        //Prepare parameters
        $parameters = $this->_prepareImportParameters(array('import_file' => $csv));
        //Get response
        $report = $this->_uploadFile($pageUrl, $importUrl, $startUrl, $parameters, $fileName, $continueOnError);
        //Return messages array
        return $report;
    }

    /**
     * Perform export with current selected options
     *
     * @return array
     */
    public function export()
    {
        //Get export page full Url
        $pageUrl = $this->url();
        //Get export file full url
        $baseExportUrl = $this->getElement("//form[@id='export_filter_form']")->attribute('action');
        $exportUrl = $baseExportUrl . $this->_getExportFileUrl();
        //Prepare parameters array
        $parameters = $this->_prepareExportParameters();
        //Prepare Skipped attributes array
        $parameters = $this->_prepareExportSkipAttributes($parameters);
        //Get CSV file
        $report = $this->_getFile($pageUrl, $exportUrl, $parameters);
        //Convert Csv to array
        return $this->csvHelper()->csvToArray($report);
    }

    /**
     * Choose Import dialog options
     *
     * @param string $entityType Entity type to Import (Products/Customers/Customers Main File/
     * Customer Addresses/Customer Finances)
     * @param string $importBehavior Import behavior
     *
     * @return $this
     */
    public function chooseImportOptions($entityType, $importBehavior = null)
    {
        $this->fillDropdown('entity_type', $entityType);
        if (!is_null($importBehavior)) {
            $this->fillDropdown('import_behavior', $importBehavior);
        }

        return $this;
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
        $this->fillDropdown('entity_type', $entityType);
        $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));

        return $this;
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
        switch ($fileType) {
            case 'master':
                $needleData = $this->prepareMasterData($needleData);
                break;
            case 'address':
                $needleData = $this->prepareAddressData($needleData);
                break;
            case 'finance':
                $needleData = $this->prepareFinanceData($needleData);
                break;
        }
        $fieldsToCompare = count($needleData);
        foreach ($fileLines as $lineIndex => $line) {
            $i = 0;
            foreach ($needleData as $name => $val) {
                if (!array_key_exists($name, $line)) {
                    $this->markTestIncomplete('MAGETWO-3858');
                }
                if ($line[$name] != $val) {
                    break;
                }
                $i++;
            }
            if ($i == $fieldsToCompare) {
                return $lineIndex;
            }
        }
        return null;
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
        $excludeComparison = array('associate_to_website', 'group', 'send_welcome_email', 'send_from', 'send_from',
            'password', 'auto_generated_password');
        $convertToNumeric = array('Yes' => 1, 'Enabled' => 1, 'In Stock' => 1, 'No' => 0, '%noValue%' => 0);
        $tastyData = array();

        foreach ($excludeComparison as $excludeField) {
            if (array_key_exists($excludeField, $rawData)) {
                unset($rawData[$excludeField]);
            }
        }
        // converting Yes/No/noValue to numeric 1 or 0
        foreach ($rawData as $key => $value) {
            if (isset($convertToNumeric[$value])) {
                $rawData[$key] = $convertToNumeric[$value];
            }
        }
        // adjust attribute keys
        $customerToCsvKeys = $rawData;
        foreach ($customerToCsvKeys as $key => $value) {
            $customerToCsvKeys[$key] = $key;
        }
        $customerToCsvKeys['first_name'] = "firstname";
        $customerToCsvKeys['middle_name'] = 'middlename';
        $customerToCsvKeys['last_name'] = 'lastname';
        $customerToCsvKeys['date_of_birth'] = 'dob';
        $customerToCsvKeys['tax_vat_number'] = 'taxvat';

        // keys exchange and copying values
        foreach ($rawData as $key => $value) {
            $tastyData[$customerToCsvKeys[$key]] = $value;
        }
        return $tastyData;
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
        // convert street
        $rawData['street'] = $rawData['street_address_line_1'] . "\n" . $rawData['street_address_line_2'];

        $excludeComparison = array('country', 'street_address_line_1', 'street_address_line_2');
        $convertToNumeric = array('Yes' => 1, 'Enabled' => 1, 'In Stock' => 1, 'No' => 0, '%noValue%' => 0);
        foreach ($excludeComparison as $excludeField) {
            if (array_key_exists($excludeField, $rawData)) {
                unset($rawData[$excludeField]);
            }
        }
        // converting Yes/No/noValue to numeric 1 or 0
        foreach ($rawData as $key => $value) {
            if (isset($convertToNumeric[$value])) {
                $rawData[$key] = $convertToNumeric[$value];
            }
        }
        $tastyData = array();

        // adjust attribute keys
        $customerToCsvKeys = $rawData;
        foreach ($customerToCsvKeys as $key => $value) {
            $customerToCsvKeys[$key] = $key;
        }
        $customerToCsvKeys['first_name'] = "firstname";
        $customerToCsvKeys['last_name'] = 'lastname';
        $customerToCsvKeys['middle_name'] = 'middlename';
        $customerToCsvKeys['state'] = 'region';
        $customerToCsvKeys['zip_code'] = 'postcode';
        $customerToCsvKeys['default_billing_address'] = '_address_default_billing_';
        $customerToCsvKeys['default_shipping_address'] = '_address_default_shipping_';

        // keys exchange and copying values
        foreach ($rawData as $key => $value) {
            $tastyData[$customerToCsvKeys[$key]] = $value;
        }
        return $tastyData;
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
        //convert Store Credit to float format
        if (isset($rawData['store_credit'])) {
            $rawData['store_credit'] = (float)$rawData['store_credit'];
            $rawData['store_credit'] = number_format($rawData['store_credit'], 4, '.', '');
        }
        return $rawData;
    }

    /**
     * Apply customer attributes filter
     *
     * @param array $fieldParams
     *            example:
     *             array('attribute_label' => 'text_label', 'attribute_code' => 'text_code')))
     *
     * @return void
     */
    public function customerFilterAttributes(array $fieldParams)
    {
        //fill filter fields
        $this->fillForm($fieldParams);
        //perform search
        $this->clickButton('search', false);
        $this->pleaseWait();
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
        $gridXpath = $this->_getControlXpath('fieldset', $fieldset);
        $conditions = array();
        if (array_key_exists('attribute_label', $fieldParams)) {
            $conditions[] = "td[2][contains(text(),'{$fieldParams['attribute_label']}')]";
        }
        if (array_key_exists('attribute_code', $fieldParams)) {
            $conditions[] = "td[3][contains(text(),'{$fieldParams['attribute_code']}')]";
        }
        $rowXPath = $gridXpath . '//tr[' . implode(' and ', $conditions) . ']';
        return $this->elementIsPresent($rowXPath) ? $rowXPath : null;
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
        $gridXpath = $this->_getControlXpath('fieldset', $fieldset);
        $conditions = array();
        if (array_key_exists('attribute_label', $fieldParams)) {
            $conditions[] = "td[2][normalize-space(text())='{$fieldParams['attribute_label']}']";
        }
        if (array_key_exists('attribute_code', $fieldParams)) {
            $conditions[] = "td[3][normalize-space(text())='{$fieldParams['attribute_code']}']";
        }
        $rowXPath = $gridXpath . '//tr[' . implode(' and ', $conditions) . ']//input[@name="skip_attr[]"]';
        $availableElement = $this->elementIsPresent($rowXPath);
        if ($availableElement && $availableElement->displayed()) {
            $currentStatus = $availableElement->selected();
            if (($currentStatus && !$skip) || (!$currentStatus && $skip)) {
                $this->focusOnElement($availableElement);
                $availableElement->click();
            }
            return true;
        }
        return false;
    }

    /**
     * Get list of Customer Entity Types specific for Magento versions
     *
     * @return array
     */
    public function getCustomerEntityType()
    {
        return array('Customers Main File', 'Customer Addresses');
    }

    /**
     * Fill filter form
     *
     * @param array $data array(attribute_code => attribute_value)
     *
     * @throws Exception
     */
    public function setFilter(array $data)
    {
        foreach ($data as $attrCode => $value) {
            $this->addParameter('attr_code', $attrCode);
            if ($this->controlIsPresent('field', 'date_filter_from')) {
                $this->fillField('date_filter_from', $value['from']);
                $this->fillField('date_filter_to', $value['to']);
            } elseif ($this->controlIsPresent('field', 'input_filter')) {
                $this->fillField('input_filter', $value);
            } elseif ($this->controlIsPresent('dropdown', 'select_filter')) {
                $this->fillDropdown('select_filter', $value);
            } elseif ($this->controlIsPresent('field', 'text_filter_from')) {
                $this->fillField('text_filter_from', $value['from']);
                $this->fillField('text_filter_to', $value['to']);
            }
        }
    }
}
