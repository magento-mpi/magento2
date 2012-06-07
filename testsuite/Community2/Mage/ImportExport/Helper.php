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
     * Import data from csv file
     *
     * @param array|string $data
     * @throws Exception
     */
    public function import($data)
    {
        if (is_string($data)) {
            $data = $this->loadData($data);
        }

        if (!is_array($data)) {
            throw new Exception('Unsupported data type for import');
        }

        // Step
        $this->fillForm($data);
        //$this->pleaseWait();
        // Verify
        $this->importBeforeCheckData($data);

        // Step
        $this->clickButton('check_data', false);
        $this->pleaseWait();
        // Verify
        $this->importBeforeImport($data);

        // Step
        $this->clickButton('import', false);
        $this->pleaseWait();
        // Verify
        $this->assertMessagePresent('success', 'task_in_queue');
    }

    /**
     * Optional checks before Check Data clicked
     *
     * @param array $data
     */
    public function importBeforeCheckData(array $data)
    {
        $fieldset = $this->getCurrentUimapPage()
            ->getMainForm()
            ->findFieldset('import_settings');

        // optional checks
        switch ($data['entity_type']) {
            case 'Orders':
                // Check import file note for orders
                $xpathImportFileNote = $fieldset->findPageelement('import_file_note');
                $this->assertTrue($this->isVisible($xpathImportFileNote), 'Import file note is absent on the page.');
                break;
        }
    }

    /**
     * Optional checks before Import clicked
     *
     * @param array $data
     */
    public function importBeforeImport(array $data)
    {
        // optional checks
        switch ($data['entity_type']) {
            case 'Orders':
            case 'Products':
                // Check messages for orders
                $this->addParameter('rows', '');
                $this->addParameter('entities', '');
                $this->addParameter('errors', '');
                $this->assertMessagePresent('success', 'checked_rows');
                $this->assertMessagePresent('success', 'checked_entities');
                $this->assertMessagePresent('success', 'invalid_rows');
                $this->assertMessagePresent('success', 'total_errors');
                $this->assertMessagePresent('success', 'file_is_valid');
                break;
            default:
                throw new Exception('Unknown entity_type parameter');
        }
    }

    /**
     * Get downloaded file name with directory
     * @return NULL|string
     */
    public function getDownloadedFile()
    {
        $fullname = $this->getFileSavePath();
        if (file_exists($fullname) && !is_dir($fullname) && is_readable($fullname)) {
            return $fullname;
        }
        return NULL;
    }

    /**
     * Check if directory exists and is writable. Tries to create it, if absent.
     * Error message can be fetched from $this->getDirectoryError()
     * @param string $dir
     * @return bool
     */
    public function checkDirExistsAndWritable($dir)
    {
        if (!file_exists($dir) && !mkdir($dir, 0777, true)) {
            $this->checkDirectoryError = 'Cannot create directory ' . $dir;
            return FALSE;
        } elseif (!is_writable($dir)) {
            $this->checkDirectoryError = "Directory $dir is not writable";
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Return expected file path if it is downloaded. Implied download directory ./tmp/downloads
     * @throws Exception
     * @return string
     */
    public function getFileSavePath()
    {
        //$this->setDirectorySeparator();
        $appConfig = $this->_configHelper->getApplicationConfig();;
        $file = $this->getExportFileName();
        if (NULL == $file) {
            throw new Exception("Export file name not found on the page");
        }

        if (!array_key_exists('downloadDir', $appConfig['application'])) {
            throw new Exception("Parameter 'downloadDir' not found in app config");
        }

        return $appConfig['application']['downloadDir'] . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Override system DIRECTORY_SEPARATOR if it was mentioned in config
     */
    public  function setDirectorySeparator()
    {
        if (!defined('DS')) {
            $appConfig = $this->_configHelper->getApplicationConfig();
            define('DS', array_key_exists('overrideDS', $appConfig['application']) ?
                $appConfig['application']['overrideDS'] : DIRECTORY_SEPARATOR);
        }
    }

    /**
     * Fetch file name from export page
     * @return null|string
     */
    public function getExportFileName()
    {
        return $this->getElementByXpath($this->_getControlXpath('pageelement', 'export_file_name'), 'text');
    }

    // @TODO: for further tests functionality
    public function checkFieldsToSkip()
    {

    }

    /**
     * preconfigure export entity and fields to skip
     * @param $data
     * @throws Exception
     */
    public function exportPreconditions($data)
    {
        if (is_string($data)) {
            $data = $this->loadData($data);
        }

        if (!is_array($data)) {
            throw new Exception('Unsupported data type for export');
        }
        if (!array_key_exists('entity_type', $data)) {
            throw new Exception('Entity type field is obligatory for export data set');
        }
        //choosing entity type
        $this->fillForm(array('entity_type' => $data['entity_type'], 'file_format' => $data['file_format']));

        //@TODO: skipping fields for all entity types
//        if (array_key_exists('skip_fields', $data) && is_array($data['skip_fields'])) {
//            foreach($data['skip_fields'] as $field) {
//                $this->searchAndChoose($field, 'grid_and_filter');
//            }
//        }
    }

    /**
     *
     * @param string $fullName
     * @param int $timeOut
     */
    public function waitForFileDownload($fullName, $timeOut = NULL)
    {
        $timeOut = intval($timeOut);
        if (0 >= $timeOut) {
            $timeOut = $this->_browserTimeoutPeriod / 1000;
        }

        while ($timeOut > 0) {
            clearstatcache();
            if (file_exists($fullName) && filesize($fullName) > 0) {
                return;
            }
            sleep(1);
            $timeOut--;
        }
    }

    /**
     * @param array $fieldParams
     * @param string $fieldset
     * @return array|null
     */
    public function productAttributesSearch(array $fieldParams, $fieldset)
    {
        $sets = $this->getCurrentUimapPage()->getMainForm()->getAllFieldsets();
        $gridFieldSet = $sets[$fieldset];
        $gridXpath = $gridFieldSet->getXPath();
        $conditions = array();
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
     * Fill filter form and search
     * @param array $data
     * @throws Exception
     */
    public function setFilter($data)
    {
        foreach ($data as $code => $params) {
            // "input" is supposed to be default type for fields
            $type = array_key_exists('type', $params) ? $params['type'] : 'input';
            $tableXPath = '//table[@id="export_filter_grid_table"]';
            switch ($type) {
                case 'input':
                    $rowXpath = "$tableXPath//tr[td[3][normalize-space(text())='$code']]";
                    if (!$this->isElementPresent($rowXpath)) {
                        throw new Exception("Field '$code' not found in a filter table");
                    }
                    $params['path'] = $rowXpath . '//td[4]//input';
                    $this->_fillFormField($params);
                    break;
                case 'select':
                    $rowXpath = "$tableXPath//tr[td[3][normalize-space(text())='$code']]";
                    if (!$this->isElementPresent($rowXpath)) {
                        throw new Exception("Field '$code'' not found in a filter table");
                    }
                    $params['path'] = $rowXpath . '//td[4]//select';
                    $this->_fillFormDropdown($params);
                    break;
                case 'range':
                    throw new Exception("We cannot process ranges yet"); //@TODO
                    break;
                default:
                    throw new Exception("Unknown field type $type for filter");
                    break;
            }
        }
    }

    public function continueExport()
    {
        $this->clickButton('continue');
        $queueMessage = $this->_getControlXpath('message', 'task_in_queue');
        $this->assertTrue($this->isElementPresent($queueMessage), 'Export task seems to be not sent to queue');
        //waiting for queue 5x longer(and converting milliseconds to seconds)
        $downloaderBlock = $this->_getControlXpath('pageelement', 'downloader_block');
        $this->waitForElementVisible($downloaderBlock, 5 * $this->_browserTimeoutPeriod / 1000);
    }

    /**
     * @throws Exception
     */
    public function clickAndLoadFile()
    {
        $this->continueExport();
        // cleaning expected file path to avoid conflicts
        $expectedFile = $this->getFileSavePath();
        if (empty($expectedFile)) {
            $this->fail('Don\'t know which file to expect');
        }
        if (file_exists($expectedFile)) {
            unlink($expectedFile);
        }

        //downloading file
        // !!!WARNING!!! Auto saving for CSV files should be turned ON in your browser
        // otherwise this and all following tests will hang up waiting for dialog window click
        $this->clickButton('download', false);
        $this->waitForFileDownload($expectedFile);
    }

    /**
     * @param array|string $exportData
     * @param array $filterData
     */
    public function exportWithFilter($exportData, $filterData)
    {
        $this->exportPreconditions($exportData);
        $this->setFilter($filterData);
        $this->clickAndLoadFile();
    }

    protected function fetchFileUrlFromPage()
    {
        $jsCode = <<<CODE
        downloader = this.browserbot.getCurrentWindow().document.getElementById('export_file_downloader_file_exist');
        buttons = downloader.getElementsByTagName('button');
        buttons[0].getAttribute('onclick');
CODE;

        $button = $this->getEval($jsCode);
        $marker = 'window.location=';
        if (strpos($button, $marker) !== 0) {
            throw new Exception('Unexpected button onclick attribute: ' . $button);
        }
        $url = trim(substr($button, strlen($marker)), "'");
        return $url;
    }

    /**
     * General function for export any suitable entity
     * @param array|string $data
     * @throws Exception
     */
    public function export($data)
    {
        $this->exportPreconditions($data);
        $this->clickAndLoadFile();
    }

    /**
     * Read CSV file into array
     *
     * @param string $fileName
     * @return array
     */
    public function readCsvFile($fileName)
    {
        $this->assertTrue(file_exists($fileName) && is_file($fileName),
            'CSV file doesn\'t exist or isn\'t readable. Expected file path: ' . $fileName);

        $file = array();
        $handle = fopen($fileName, "r");
        $this->assertTrue($handle !== FALSE, 'Cannot open CSV file ' . $fileName);
        while (TRUE) {
            $data = fgetcsv($handle, 0, ",");
            if ($data === FALSE) {
                break;
            }
            $file[] = $data;
        }
        fclose($handle);

        $this->assertTrue(isset($file[0]) && is_array($file[0]), 'Wrong format of CSV file ' . $fileName);

        $rows = array();
        foreach ($file as $row => $columns) {
            if ($row == 0) {
                continue;
            }
            $this->assertEquals(count($file[0]), count($columns), 'Wrong format of CSV file ' . $fileName);
            $rows[$row - 1] = array_combine($file[0], $columns);
        }

        return $rows;
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
        $ctnKeys = array_keys($convertToNumeric);

        $customerToCsvKeys = array(
            'prefix' => 'prefix',
            'first_name' => "firstname",
            'middle_name' => "middlename",
            'last_name' => "lastname",
            'email' => 'email',
            'gender' => 'gender',
            'date_of_birth' => "dob",
            'tax_vat_number' => "taxvat"
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

        // keys exchange and copying values
        foreach ($rawData as $key => $value) {
            $tastyData[$customerToCsvKeys[$key]] = $value;
        }

        return $tastyData;
    }

    /**
     * @param array $needleData
     * @param array $fileLines
     * @return int
     */
    public function lookForEntity($needleData, $fileLines)
    {
        $needleData = $this->prepareMasterData($needleData);
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
     * Remove all files in the specified directory by certain mask
     * @param $dir
     * @param string $mask
     */
    public function removeAllFilesByMask($dir, $mask)
    {
        $files = scandir($dir);
        if (FALSE === $files) {
            throw new Exception("$dir is not a directory");
        }
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($dir . DIRECTORY_SEPARATOR . $file) && fnmatch($mask, $file)) {
                    unlink($dir . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
    }

    /**
     * Returns directory name where csv files are downloaded
     */
    public function getCsvDownloadsDirectory()
    {
        $projectPath = substr(getcwd(), 0, strpos(getcwd(), 'testsuite'));
        $downloadPath = $projectPath . 'fixture' . DIRECTORY_SEPARATOR . 'community2' . DIRECTORY_SEPARATOR . 'core'
            . DIRECTORY_SEPARATOR . 'Mage' . DIRECTORY_SEPARATOR . 'ImportExport';
        echo $downloadPath;
    }

    /**
     * Write array into CSV file
     *
     * @param string $fileName
     * @param array $array
     */
    public function writeCsvFile($fileName, array $array)
    {
        $handle = fopen($fileName, "w");
        $this->assertTrue($handle !== FALSE, 'Cannot open CSV file ' . $fileName);
        fputcsv($handle, array_keys($array[0]));
        foreach ($array as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);
    }

    /**
     * Checks import settings area
     *
     */
    public function checkImportSettingsArea()
    {
        $fieldset = $this->getCurrentUimapPage()
            ->getMainForm()
            ->findFieldset('import_settings');

        // Check area
        $xpathArea = $fieldset->getXpath();
        $this->assertTrue($this->isElementPresent($xpathArea), 'Import Settings Area is absent on the page.');

        // Check entity type
        $xpathEntityType = $fieldset->findDropdown('entity_type');
        $this->assertTrue($this->isElementPresent($xpathEntityType), 'Entity type dropdown is absent on the page.');

        // Check import behavior
        $xpathImportBehavior = $fieldset->findDropdown('import_behavior');
        $this->assertTrue($this->isElementPresent($xpathImportBehavior), 'Import behavior dropdown is absent on the page.');

        // Check file to import
        $xpathFileToImport = $fieldset->findField('file_to_import');
        $this->assertTrue($this->isElementPresent($xpathFileToImport), 'File to import field is absent on the page.');
    }

    /**
     * Checks export settings area
     *
     */
    public function checkExportSettingsArea()
    {
        $fieldset = $this->getCurrentUimapPage()
            ->getMainForm()
            ->findFieldset('export_settings');

        // Check area
        $xpathArea = $fieldset->getXpath();
        $this->assertTrue($this->isElementPresent($xpathArea), 'Export Settings Area is absent on the page.');

        // Check entity type
        $xpathEntityType = $fieldset->findDropdown('entity_type');
        $this->assertTrue($this->isElementPresent($xpathEntityType), 'Entity type dropdown is absent on the page.');

        // Check file format
        $xpathFileFormat = $fieldset->findDropdown('file_format');
        $this->assertTrue($this->isElementPresent($xpathFileFormat), 'File format dropdown is absent on the page.');
    }

    /**
     * Finds row number in File Array
     *
     * @param array $file
     * @param array $order
     * @return int
     */
    public function findOrderRowInFileData(array $file, array $order)
    {
        $orderLine = null;
        foreach ($file as $row => $columns) {
            if (isset($columns['increment_id']) && $order['order_id']
                && $columns['increment_id'] == $order['order_id']) {
                $orderLine = $row;
                break;
            }
        }

        return $orderLine;
    }

    /**
     * Checks exported order
     *
     * @param string $fileName
     * @param array $product
     * @param array $order
     */
    public function checkExportedOrder($fileName, array $product, array $order)
    {
        // Step
        $file = $this->readCsvFile($fileName);
        $orderLine = $this->findOrderRowInFileData($file, $order);
        // Verifying
        $this->assertNotNull($orderLine, 'Could not find order line.');

        $line = $file[$orderLine];

        $this->assertTrue(isset($line['shipping_description']), 'Could not find order shipping description column.');
        $shipping = $order['shipping_data']['shipping_service'] . ' - ' . $order['shipping_data']['shipping_method'];
        $this->assertEquals($shipping, $line['shipping_description'],
            'Order shipping description column differs from stored one.');

        $this->assertTrue(isset($line['subtotal']), 'Could not find order subtotal column.');
        $this->assertEquals($product['prices_price'], $line['subtotal'],
            'Order subtotal column differs from stored one.');

        $this->assertTrue(isset($line['_order_item_name']), 'Could not find order item name column.');
        $this->assertEquals($product['general_name'], $line['_order_item_name'],
            'Order item name column differs from stored one.');

        $this->assertTrue(isset($line['_order_item_sku']), 'Could not find order item sku column.');
        $this->assertEquals($product['general_sku'], $line['_order_item_sku'],
            'Order item sku column differs from stored one.');

        $this->assertTrue(isset($line['_order_item_price']), 'Could not find order item price column.');
        $this->assertEquals($product['prices_price'], $line['_order_item_price'],
            'Order item price column differs from stored one.');

        $this->assertTrue(isset($line['_order_item_qty_ordered']), 'Could not find order item qty ordered column.');
        $this->assertEquals(1, $line['_order_item_qty_ordered'],
            'Order item qty ordered column differs from stored one.');

        $this->assertTrue(isset($line['_order_item_row_total']), 'Could not find order item row total column.');
        $this->assertEquals($product['prices_price'], $line['_order_item_row_total'],
            'Order item row total column differs from stored one.');

        $this->assertTrue(isset($line['_order_address_firstname']), 'Could not find order address firstname column.');
        $this->assertEquals($order['billing_addr_data']['billing_first_name'], $line['_order_address_firstname'],
            'Order address firstname column differs from stored one.');

        $this->assertTrue(isset($line['_order_address_lastname']), 'Could not find order address lastname column.');
        $this->assertEquals($order['billing_addr_data']['billing_last_name'], $line['_order_address_lastname'],
            'Order address lastname column differs from stored one.');

        $this->assertTrue(isset($line['_order_address_street']), 'Could not find order address street column.');
        $this->assertEquals($order['billing_addr_data']['billing_street_address_1'], $line['_order_address_street'],
            'Order address street column differs from stored one.');

        $this->assertTrue(isset($line['_order_address_city']), 'Could not find order address city column.');
        $this->assertEquals($order['billing_addr_data']['billing_city'], $line['_order_address_city'],
            'Order address city column differs from stored one.');

        $this->assertTrue(isset($line['_order_address_region']), 'Could not find order address region column.');
        $this->assertEquals($order['billing_addr_data']['billing_state'], $line['_order_address_region'],
            'Order address region column differs from stored one.');

        $this->assertTrue(isset($line['_order_address_postcode']), 'Could not find order address postcode column.');
        $this->assertEquals($order['billing_addr_data']['billing_zip_code'], $line['_order_address_postcode'],
            'Order address postcode column differs from stored one.');

        $this->assertTrue(isset($line['_order_address_telephone']), 'Could not find order address telephone column.');
        $this->assertEquals($order['billing_addr_data']['billing_telephone'], $line['_order_address_telephone'],
            'Order address telephone column differs from stored one.');
    }

    /**
     * Edits status and comment in exported file
     *
     * @param string $fileName
     * @param array $order
     */
    public function editExportedOrderStatusAndComment($fileName, array $order)
    {
        // Step
        $file = $this->readCsvFile($fileName);
        $orderLine = $this->findOrderRowInFileData($file, $order);
        // Verifying
        $this->assertNotNull($orderLine, 'Could not find order line.');
        $this->assertTrue(isset($file[$orderLine]['status']), 'Could not find order status column.');
        $this->assertTrue(isset($file[$orderLine]['_order_status_history_comment']),
            'Could not find order comments column.');

        // Step
        $file[$orderLine]['status'] = strtolower($order['status']);
        $file[$orderLine]['_order_status_history_comment'] = $order['status_history_comment'];
        $file = $this->writeCsvFile($fileName, $file);
    }
}
