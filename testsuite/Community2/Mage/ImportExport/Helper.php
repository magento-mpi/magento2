<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Generate URL for selected area
     *
     * @param string $uri
     * @param null|array $params
     * @return string
     */
    public function getUrl($uri, $params = null)
    {
        $baseUrl = $this->_configHelper->getBaseUrl();
        $baseUrl = rtrim($baseUrl, '/');
        $uri = ltrim($uri, '/');
        return  $baseUrl . '/' . $uri . (is_null($params)?'': '?' . http_build_query($params));
    }

    /**
     * Get file from admin area
     * Suitable for reports testing
     *
     * @param string $urlPage Url to the page for defining form key
     * @param string $url Url to the file or submit form
     * @param araay $parameters Submit form parameters
     * @return string
     */
    public function getFile($urlPage, $url, $parameters = array())
    {
        $cookie = $this->getCookie();
        $ch = curl_init();
        //open export page and get from key
        curl_setopt($ch, CURLOPT_URL, $urlPage);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE,$cookie);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $data = curl_exec($ch);
        //get form_key
        $body=substr($data,curl_getinfo($ch,CURLINFO_HEADER_SIZE));
        preg_match('/<form id="export_filter_form".*\s*<input\sname="form_key"\stype="hidden"\svalue="(.*)"/i',
                $body, $data);
        //prepare request
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE,$cookie);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 120);
        //prepare parameters
        $fields_string = '';
        foreach($parameters as $key=>$value)
        {  if (is_array($value))
           {
               foreach ($value as $attrID) {
                    $fields_string .= $key.'='.urlencode($attrID).'&';
               }
           } else {
               $fields_string .= $key.'='.urlencode($value).'&';
           }
        }
        rtrim($fields_string,'&');
        $fields_string = "form_key={$data[1]}&frontend_label=&" . $fields_string;
        //put parameters
        curl_setopt($ch, CURLOPT_POST, count($fields_string));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        //request file
        $data = curl_exec($ch);
        //get body
        $body=substr($data,curl_getinfo($ch,CURLINFO_HEADER_SIZE));
        curl_close($ch);
        return $body;
    }
    /**
     * Prepare parameters array for getFile method and Export functionality
     *
     * @param array $parameters
     * @return array
     */
    public function _prepareParameters($parameters = array()) {
            //prepare parameters array
            $elementTypes = array('input','select');
            foreach ($elementTypes as $elementType) {
                $tablePath = "css=table#export_filter_grid_table>tbody>tr>td.last>{$elementType}[name*='export_filter']";
                $size = $this->getXpathCount($tablePath);
                for($i=0;$i<$size;$i++){
                //get attributes filters and values array
                $attName = $this->getAttribute($tablePath . ":nth({$i})@name");
                switch ($elementType) {
                    case 'input':
                        $attValue = $this->getText($tablePath . ":nth({$i})");
                        break;
                    case 'select':
                        break;
                        $attValue = $this->getSelectedValue($tablePath . ":nth({$i})");
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
    public function _prepareSkipAttributes($parameters = array()) {
            //collect skip attributes
            $tablePath = "css=table#export_filter_grid_table>tbody>tr>td>input[name='skip_attr[]']";
            $size = $this->getXpathCount($tablePath);
            $parameters['skip_attr[]'] = array();
            for($i=0;$i<$size;$i++){
            if ($this->isChecked($tablePath . ":nth({$i})")){
                //get attribute id
                $attID = $this->getValue($tablePath . ":nth({$i})");
                //save ittribute id, invers saving
                $parameters['skip_attr[]'][]=$attID;
            }
            }
            if (count($parameters['skip_attr[]'])==0){
                unset($parameters['skip_attr[]']);
            }
            return $parameters;
    }
    public function _getExportFileUrl() {
        $entity_type = $this->getSelectedValue(
                $this->_getControlXpath('dropdown','entity_type'));
        $path = '/export/entity/' . $entity_type;
        $path = $path . '/file_format/' .
                $this->getSelectedValue(
                        $this->_getControlXpath('dropdown','file_format'));
        if ($this->controlIsVisible('dropdown', 'export_file')){
           $path = $path . "/{$entity_type}_entity/" .
                   $this->getSelectedValue(
                           $this->_getControlXpath('dropdown','export_file'));
        }
        return $path;
    }
    /**
     * Convert CSV string to array
     *
     * @param string $input Input csv string to be converted to array
     * @param string $delimiter Delimiter
     * @return array
     */
    function csvToArray($input, $delimiter=',')
    {
        $data = array();
        $header = null;
        $csvData = str_getcsv($input, "\n");
        foreach($csvData as $csvLine){
            $row = str_getcsv($csvLine, $delimiter);
            if (!$header){
                $header = $row;
            } else {
                $data[] = array_combine($header, $row);
            }
        }
        return $data;
    }
    /**
     * Perform export with current selected options
     * 
     * @return array
     */
    public function export() {
       //get export page full Url
       $pageUrl = $this->getUrl($this->getCurrentUimapPage()->getMca());
       //get export file full url
       $exportUrl = $pageUrl . $this->_getExportFileUrl();
               //prepare parameters array
       $parameters = $this->_prepareParameters();
       $parameters = $this->_prepareSkipAttributes($parameters);
       //get CSV file
       $report = $this->getFile($pageUrl,$exportUrl,$parameters);
        //convert Csv to array
       return $this->csvToArray($report);
    }

    /**
     * @param string $fileType File type (master|address|finance)
     * @param array $needleData Customer/Address/Finance
     * @param array $fileLines Array from csv files
     * @return int
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
            if ($i + 1 == $fieldsToCompare) {
                return $lineIndex;
            }
        }
        return null;
    }

    /**
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
            'Yes' => 1,
            'Enabled' => 1,
            'In Stock' => 1,
            'No' => 0,
            '%noValue%' => 0
        );

        $tastyData = array();

        foreach ($excludeFromComparison as $excludeField) {
            if (array_key_exists($excludeField, $rawData)) {
                unset($rawData[$excludeField]);
            }
        }

        // converting Yes/No/noValue to numeric 1 or 0
        foreach ($rawData as &$value) {
            if (isset($convertToNumeric[$value])) {
                $value = $convertToNumeric[$value];
            }
        }

        // adjust attribute keys
        foreach ($rawData as $key => $value) {
            $customerToCsvKeys[$key] = $value;
        }
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
     * @param $rawData
     * @return array
     */
    public function prepareAddressData($rawData) {
        //TODO
    }

    /**
     * @param $rawData
     * @return array
     */
    public function prepareFinanceData($rawData) {
        //TODO
    }
}
