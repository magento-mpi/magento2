<?php
/**
 * Magento
 *
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
class Community2_Mage_ImportExport_Helper extends Mage_Selenium_TestCase
{
    /**
     * Build full export URL
     *
     * @return string
     */
    protected function _getExportFileUrl()
    {
        $entity_type = $this->getSelectedValue(
            $this->_getControlXpath('dropdown', 'entity_type'));
        $path        = '/export/entity/' . $entity_type;
        $path        = $path . '/file_format/' .
            $this->getSelectedValue(
                $this->_getControlXpath('dropdown', 'file_format'));
        if ($this->controlIsVisible('dropdown', 'export_file')) {
            $path = $path . "/entity_subtype/" .
                $this->getSelectedValue(
                    $this->_getControlXpath('dropdown', 'export_file'));
        }
        return $path;
    }
    /**
     * Generate URL for selected area
     *
     * @param string $uri
     * @param null|array $params
     * @return string
     */
    protected function _getUrl($uri, $params = null)
    {
        $baseUrl = $this->_configHelper->getBaseUrl();
        $baseUrl = rtrim($baseUrl, '/');
        $uri     = ltrim($uri, '/');
        return $baseUrl . '/' . $uri . (is_null($params) ? '' : '?' . http_build_query($params));
    }

    /**
     * Build full import URL
     *
     * @param bool $validate Specify step of Import - Data Validate or Import
     *                       true - Data Validate
     *                       false - Import step
     * @return string
     */
    protected function _getImportFileUrl($validate = true)
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
     * @return string
     */
    protected function _getFile($urlPage, $url, $parameters = array())
    {
        $cookie = $this->getCookie();
        $ch     = curl_init();
        //Open export page and get from key
        curl_setopt($ch, CURLOPT_URL, $urlPage);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $data = curl_exec($ch);
        //Get form_key
        $formKey = $this->_getFromKey($data);
        //Prepare export request
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 120);
        //Convert parameters to string
        $fields_string = '';
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $attrID) {
                    $fields_string .= $key . '=' . urlencode($attrID) . '&';
                }
            } else {
                $fields_string .= $key . '=' . urlencode($value) . '&';
            }
        }
        rtrim($fields_string, '&');
        $fields_string = "form_key={$formKey}&frontend_label=&" . $fields_string;
        //Put parameters
        curl_setopt($ch, CURLOPT_POST, count($fields_string));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        //Request export
        $data = curl_exec($ch);
        curl_close($ch);
        //Return response
        return $data;
    }

    /**
     * Return Form key
     *
     * @param string $pageHTML HTML response from the server
     * @return string
     */
    protected function _getFromKey($pageHTML)
    {
        $formKey = '';
        //Load page HTML to Dom model
        $dom = new DOMDocument;
        @$dom->loadHTML($pageHTML);
        //Find form key
        $domXPath = new DOMXPath($dom);
        $formKeyFilter = $domXPath->query("//div/input[@name='form_key' and @type='hidden']");
        if ($formKeyFilter){
            //Get first found form key
            $formKey = $formKeyFilter->item(0)->getAttribute('value');
        }
        //Return form key
        return $formKey;
    }
    /**
     * Return Form key
     *
     * @param string $response HTML response from the server
     * @return void
     */
    protected function _parseResponseMessages($response)
    {
        $dom = new DOMDocument;
        //Check fatal error
        if (preg_match('/Fatal error/i', $response, $result) == true
            || $response==''){
            $this->addMessage('error', $response);
            return;
        };
        //parse response
        preg_match('/{"import_validation_messages":(".*")}/i', $response, $result);
        if (!isset($result[1])){
            preg_match('/{"import_validation_container_header":(".*")}/i', $response, $result);
            preg_match('/"import_validation_messages":"(.*)"/i', $result[1], $result);
        }
        $result = stripcslashes($result[1]);
        $dom->loadHTML($result);
        $domXPath = new DOMXPath($dom);
        $filtered = $domXPath->query("//ul[@class='messages']/li[@class]");
        foreach ($filtered as $message){
            //get message type
            $messageType = $message->getAttribute('class');
            //notice-msg, success-msg, error-msg
            //get message
            $filteredMessages = $domXPath->query("//ul[@class='messages']/li[@class='{$messageType}']/ul/li/span");
            //get text
            foreach ($filteredMessages as $filteredMessage){
                $messageText = $filteredMessage->nodeValue;
                switch($messageType){
                    case 'notice-msg':
                        $this->addMessage('validation',$messageText);
                        break;

                    case 'success-msg':
                        $this->addMessage('success',$messageText);
                        break;

                    case 'error-msg':
                        $this->addMessage('error',$messageText);
                        break;

                    default:
                        $this->addMessage('error','Unexpected message: ' . $messageText);
                        break;
                }
            }
        }
    }

    /**
     * Prepare import parameters array for uploadFile method and Export functionality
     *
     * @param array $parameters
     * @return array
     */
    protected function _prepareImportParameters($parameters = array())
    {
        $entityType = $this->getSelectedValue(
            $this->_getControlXpath('dropdown', 'entity_type'));
        $parameters['entity'] = $entityType;
        $entityBehavior = $this->getSelectedValue(
            $this->_getControlXpath('dropdown', 'import_behavior'));
        $parameters['behavior'] = $entityBehavior;
        $fileFormat = $this->getSelectedValue(
            $this->_getControlXpath('dropdown', 'import_file_version'));
        $parameters['file_format_version'] = $fileFormat;
        if ($this->controlIsVisible('dropdown','import_customer_entity')){
            $customerEntity = $this->getSelectedValue(
                 $this->_getControlXpath('dropdown', 'import_customer_entity'));
            $parameters['customer_entity'] = $customerEntity;
        }
        return $parameters;
    }

    /**
     * Prepare export parameters array for getFile method and Export functionality
     *
     * @param array $parameters
     * @return array
     */
    protected function _prepareExportParameters($parameters = array())
    {
        $elementTypes = array('input','select');
        foreach ($elementTypes as $elementType) {
            $tablePath = "css=table#export_filter_grid_table>tbody>tr>td.last>{$elementType}[name*='export_filter']";
            $size = $this->getXpathCount($tablePath);
            for($i = 0; $i < $size; $i++){
                $attValue = '';
                //Get attributes filters and values array
                $tableElementPath = $tablePath . ":nth({$i})";
                $attName = $this->getAttribute($tableElementPath . '@name');
                switch ($elementType) {
                    case 'input':
                        $attValue = $this->getValue($tableElementPath);
                        break;

                    case 'select':
                        $attValue = $this->getSelectedValue($tableElementPath);
                        break;

                    default:
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
     * @return array
     */
    protected function _prepareExportSkipAttributes($parameters = array())
    {
        //Collect skip attributes
        $tablePath = "css=table#export_filter_grid_table>tbody>tr>td>input[name='skip_attr[]']";
        $size = $this->getXpathCount($tablePath);
        $parameters['skip_attr[]'] = array();
        for($i=0;$i<$size;$i++){
            if ($this->isChecked($tablePath . ":nth({$i})")){
                //get attribute id
                $attID = $this->getAttribute($tablePath . ":nth({$i})" . '@value');
                //save attribute id, inverse saving
                $parameters['skip_attr[]'][]=$attID;
            }
        }
        if (count($parameters['skip_attr[]'])==0){
            unset($parameters['skip_attr[]']);
        }
        return $parameters;
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
    protected function _uploadFile($urlPage, $importUrl, $startUrl, $parameters = array(), $fileName, $continueOnError = true)
    {
        $cookie = $this->getCookie();
        $ch     = curl_init();
        //Open import page
        curl_setopt($ch, CURLOPT_URL, $urlPage);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $data = curl_exec($ch);
        //Get form key from the page
        $formKey = $this->_getFromKey($data);
        //Add request parameters
        $parameters['form_key'] = $formKey;
        //Make tmp file
        $tempFile = $this->_testConfig->getHelper('config')->getLogDir() . DIRECTORY_SEPARATOR .
            (is_null($fileName) ? 'customer_' . date('Ymd_His') . '.csv' : $fileName);
        $handle   = fopen($tempFile, 'w+');
        fwrite($handle, $parameters['import_file']);
        fclose($handle);
        //Add request parameters
        $parameters['import_file'] = "@" . $tempFile;
        //Prepare Check Data request
        curl_setopt($ch, CURLOPT_URL, $importUrl . $formKey);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 120);
        //Put parameters
        curl_setopt($ch, CURLOPT_POST, count($parameters));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        //Request Check Data
        $data = curl_exec($ch);
        //Parse response messages
        $importMessages = array();
        $this->clearMessages();
        $this->_parseResponseMessages($data);
        //Save Check Data messages to validation array
        $importMessages['validation'] = $this->getParsedMessages();
        //verify validation message
        $continueImport = false;
        $importErrorOccurred = false;
        if (isset($importMessages['validation']['validation'])){
            foreach ($importMessages['validation']['validation'] as $validationMessage)
                if (preg_match('/Checked rows: (\d+), checked entities: (\d+), invalid rows: (\d+), total errors: (\d+)/i',
                    $validationMessage, $result) != false
                ) {
                    //compare checked and invalid rows
                    $continueImport = intval($result[1]) > intval($result[3]);
                    $importErrorOccurred = intval($result[1]) <> intval($result[3]);
                }
        }
        //Perform Import if Check Data is passed
        if ($continueImport){
            if (!$importErrorOccurred || ($importErrorOccurred && $continueOnError)){
                //Prepare request Import Data
                $parameters['import_file'] = "type=application/octet-stream";
                //Request Import data
                curl_setopt($ch, CURLOPT_URL, $startUrl . $formKey);
                curl_setopt($ch, CURLOPT_POST, count($parameters));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                $data = curl_exec($ch);
                //Parse response messages
                $this->clearMessages();
                $this->_parseResponseMessages($data);
                //Save Import messages to import array
                $importMessages['import'] = $this->getParsedMessages();
            }
        }
        //Close curl connection
        curl_close($ch);
        //Clear page messages
        $this->clearMessages();
        //Delete temp file
        unlink($tempFile);
        //Return messages
        return $importMessages;
    }

    /**
     * Convert CSV string to associative array
     *
     * @param string $input Input csv string to be converted to array
     * @param string $delimiter Delimiter
     * @return array
     */
    public function csvToArray($input, $delimiter = ',')
    {
        $temp = tmpfile();
        fwrite($temp, $input);
        fseek($temp, 0);
        $data   = array();
        $header = null;
        while (($line = fgetcsv($temp, 10000, $delimiter, '"', '\\')) !== FALSE) {
            if (!$header) {
                $header = $line;
            } else {
                try {
                    $data[] = array_combine($header, $line);
                } catch (Exception $e) {
                    //invalid format
                    return null;
                }
            }
        }
        return $data;
    }
    /**
     * Convert associative array to CSV string
     *
     * @param array $input Input associative array to be converted to string
     * @param string $delimiter Delimiter
     * @return string
     */
    public function arrayToCsv(array $input, $delimiter = ',')
    {
        $temp = tmpfile();
        $header = null;
        foreach($input as $line){
            if (!$header){
                $header = array_keys($line);
                fputcsv($temp, $header, $delimiter, '"');
            }
            fputcsv($temp, array_values($line), $delimiter, '"');
        }
        fseek($temp, 0);
        $csv = '';
        while (!feof($temp)){
            $csv .= fread($temp, 1000);
        }
        return $csv;
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
        //Get export page full Url
        $pageUrl = $this->_getUrl($this->getCurrentUimapPage()->getMca());
        //Get export file full url
        $importUrl = $pageUrl . $this->_getImportFileUrl();
        $startUrl = $pageUrl . $this->_getImportFileUrl(false);
        //Convert array to CSV
        $csv = $this->arrayToCsv($data);
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
        $pageUrl = $this->_getUrl($this->getCurrentUimapPage()->getMca());
        //Get export file full url
        $exportUrl = $pageUrl . $this->_getExportFileUrl();
        //Prepare parameters array
        $parameters = $this->_prepareExportParameters();
        //Prepare Skipped attributes array
        $parameters = $this->_prepareExportSkipAttributes($parameters);
        //Get CSV file
        $report = $this->_getFile($pageUrl, $exportUrl, $parameters);
        //Convert Csv to array
        return $this->csvToArray($report);
    }
    /**
     * Choose Import dialog options
     *
     * @param string $entityType Entity type to Import (Products/Customers)
     * @param string $importBehavior Import behavior
     * @param string $importVersion Import version (Magento 2.0 format or Magento 1.7 format)
     * @param string $importEntity Import entity for Magento 2.0 format
     * @param string $fileName Import file name
     *
     * @return $this
     */
    public function chooseImportOptions($entityType, $importBehavior = Null, $importVersion = Null, $importEntity = Null,
                                        $fileName = Null){

        $this->fillDropdown('entity_type', $entityType);
        if (!is_null($importVersion)){
            if (!$this->waitForElementVisible(
                $this->_getControlXpath('dropdown','import_file_version')))
            {
                $this->fail('Can\'t find element: dropdown - import_file_version');
            };
            $this->fillDropdown('import_file_version', $importVersion);
            if (!is_null($importBehavior)){
                if (!$this->waitForElementVisible(
                    $this->_getControlXpath('dropdown', 'import_behavior')))
                {
                    $this->fail('Can\'t find element: dropdown - import_behavior');
                };
                $this->fillDropdown('import_behavior', $importBehavior);
                if (!is_null($importEntity)){
                    if (!$this->waitForElementVisible(
                        $this->_getControlXpath('dropdown', 'import_customer_entity')))
                    {
                        $this->fail('Can\'t find element: dropdown - import_customer_entity');
                    }
                    $this->fillDropdown('import_customer_entity', $importEntity);
                }
            }
        }
        if (!is_null($fileName) && $this->controlIsVisible('field', 'file_to_import'))
            $this->fillField('file_to_import', $fileName);

        return $this;
    }
    /**
     * Choose Export dialog options
     *
     * @param string $entityType Entity type to Export (Products/Customers)
     * @param string $exportVersion Export version (Magento 2.0 format or Magento 1.7 format)
     * @param string $exportEntity Export entity for Magento 2.0 format
     *
     * @return $this
     */
    public function chooseExportOptions($entityType, $exportVersion = Null, $exportEntity = Null){

        $this->fillDropdown('entity_type', $entityType);
        if (!is_null($exportVersion)){
            if (!$this->waitForElementVisible(
                $this->_getControlXpath('dropdown','export_file_version')))
            {
                $this->fail('Can\'t find element: dropdown - export_file_version');
            }
            $this->fillDropdown('export_file_version', $exportVersion);
            if (!is_null($exportEntity)){
                if (!$this->waitForElementVisible(
                    $this->_getControlXpath('dropdown', 'export_file')))
                {
                    $this->fail('Can\'t find element: dropdown - export_file');
                }
                $this->fillDropdown('export_file', $exportEntity);
                if (!$this->waitForElementVisible(
                    $this->_getControlXpath('fieldset', 'grid_and_filter')))
                {
                    $this->fail('Can\'t find element: fieldset - grid_and_filter');
                }
                if (!$this->waitForElementVisible(
                    $this->_getControlXpath('button', 'continue')))
                {
                    $this->fail('Can\'t find element: button - continue');
                }
            }
        }

        return $this;
    }
    /**
     * Search customer/address/finance line in exported array
     * Returns line index
     *
     * @param string $fileType File type (master|address|finance)
     * @param array $needleData Main/Address/Finance line data
     * @param array $fileLines Array from csv file
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
     * @return array
     */
    public function prepareMasterData($rawData)
    {
        $excludeFromComparison = array(
            'associate_to_website',
            'group',
            'send_welcome_email',
            'send_from',
            'send_from',
            'password',
            'auto_generated_password'
        );

        $convertToNumeric = array(
            'Yes'       => 1,
            'Enabled'   => 1,
            'In Stock'  => 1,
            'No'        => 0,
            '%noValue%' => 0
        );

        $tastyData = array();

        foreach ($excludeFromComparison as $excludeField) {
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
        $customerToCsvKeys['first_name']     = "firstname";
        $customerToCsvKeys['middle_name']    = 'middlename';
        $customerToCsvKeys['last_name']      = 'lastname';
        $customerToCsvKeys['date_of_birth']  = 'dob';
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
     * @return array
     */
    public function prepareAddressData($rawData)
    {
        // convert street
        $rawData['street'] = $rawData['street_address_line_1'] . "\n" . $rawData['street_address_line_2'];

        $excludeFromComparison = array(
            'country',
            'street_address_line_1',
            'street_address_line_2'
        );

        $convertToNumeric = array(
            'Yes'       => 1,
            'Enabled'   => 1,
            'In Stock'  => 1,
            'No'        => 0,
            '%noValue%' => 0
        );


        foreach ($excludeFromComparison as $excludeField) {
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
     * @return array
     */
    public function prepareFinanceData($rawData)
    {
        //convert Store Credit to float format
        if (isset($rawData['store_credit'])){
            $rawData['store_credit'] = (float)$rawData['store_credit'];
            $rawData['store_credit'] = number_format($rawData['store_credit'],4,'.','');
        }
        return $rawData;
    }

    /**
     * Apply customer attributes filter
     * @param array $fieldParams
     *            example:
     *             array('attribute_label' => 'text_label', 'attribute_code' => 'text_code')))
     * @return void
     */
    public function customerFilterAttributes(array $fieldParams)
    {
        //fill filter fields
        $this->fillForm($fieldParams);
        //perform search
        $this->clickButton('search', false);
        $this->waitForAjax();
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
        $sets         = $this->getCurrentUimapPage()->getMainForm()->getAllFieldsets();
        $gridFieldSet = $sets[$fieldset];
        $gridXpath    = $gridFieldSet->getXPath();
        $conditions   = array();
        if (array_key_exists('attribute_label', $fieldParams)) {
            $conditions[] = "td[2][contains(text(),'{$fieldParams['attribute_label']}')]";
        }
        if (array_key_exists('attribute_code', $fieldParams)) {
            $conditions[] = "td[3][contains(text(),'{$fieldParams['attribute_code']}')]";
        }
        $rowXPath = $gridXpath . '//tr[' . implode(' and ', $conditions) . ']';
        return $this->getElementByXpath($rowXPath);
    }

    /**
     * Mark attribute as skipped
     *
     * @param array $fieldParams
     * @param string $fieldset
     * @param bool $skip
     * @return bool
     */
    public function customerSkipAttribute(array $fieldParams, $fieldset, $skip = true)
    {
        $sets         = $this->getCurrentUimapPage()->getMainForm()->getAllFieldsets();
        $gridFieldSet = $sets[$fieldset];
        $gridXpath    = $gridFieldSet->getXPath();
        $conditions   = array();
        if (array_key_exists('attribute_label', $fieldParams)) {
            $conditions[] = "td[2][contains(text(),'{$fieldParams['attribute_label']}')]";
        }
        if (array_key_exists('attribute_code', $fieldParams)) {
            $conditions[] = "td[3][contains(text(),'{$fieldParams['attribute_code']}')]";
        }
        $rowXPath = $gridXpath . '//tr[' . implode(' and ', $conditions) . ']/td/input[@name="skip_attr[]"]';
        if ($this->isElementPresent($rowXPath) && $this->isVisible($rowXPath)) {
            $currentStatus = $this->isChecked($rowXPath);
            if (($currentStatus && !$skip) || (!$currentStatus && $skip)) {
                $this->click($rowXPath);
            }
        } else {
            return false;
        }
        return true;
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
     * @throws Exception
     */
    public function setFilter($data)
    {
        foreach ($data as $attr_code => $value) {
            $this->addParameter('attr_code', $attr_code);
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
