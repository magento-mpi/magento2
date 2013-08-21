<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tags
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Reports_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Searches the specified data in the report.
     * Returns row number(s) if found
     *
     * @param array $data Data to look for in report
     * @param string $gridXpath
     *
     * @return array
     */
    public function searchDataInReport(array $data, $gridXpath = 'report_tag_grid')
    {
        $rowNumbers = array();
        $totalCount = $this->getTotalRecordsInTable('fieldset', $gridXpath);
        $xpathTR = $this->formSearchXpath($data);
        if ($this->elementIsPresent($xpathTR)) {
            for ($i = 1; $i <= $totalCount; $i++) {
                if ($this->elementIsPresent(str_replace('tr', 'tr[' . $i . ']', $xpathTR))) {
                    $rowNumbers[] = $i;
                }
            }
        }
        return $rowNumbers;
    }

    /**
     * Verifies if report sorting by specific column is correct
     *
     * @param array $data Data to look for in report
     * @param string $column Column that report is sorted by
     *
     * @return void
     */
    public function verifyReportSortingByColumn(array $data, $column)
    {
        $sortedReport = $data;

        for ($i = 0; $i < count($data) - 1; $i++) {
            for ($j = 0; $j < count($data) - 1; $j++) {
                $columnValues = array($sortedReport[$j][$column], $sortedReport[$j + 1][$column]);
                $columnValuesSorted = $columnValues;
                sort($columnValuesSorted);
                if ($columnValues != $columnValuesSorted) {
                    $rowTemp = $sortedReport[$j];
                    $sortedReport[$j] = $sortedReport[$j + 1];
                    $sortedReport[$j + 1] = $rowTemp;
                }
            }
        }

        for ($i = 0; $i < count($sortedReport); $i++) {
            list($rowNumber) = $this->searchDataInReport($sortedReport[$i]);
            if ($i + 1 !== $rowNumber) {
                $this->addVerificationMessage('Report sorting by ' . $column . ' is not correct. Line number must be '
                    . ($i + 1) . ' but now is ' . $rowNumber. ' Data: '. print_r($sortedReport[$i], true));
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Performs report export
     *
     * @return array|bool
     */
    public function export()
    {
        $exportLabel = $this->getControlAttribute('dropdown', 'export_to', 'selectedLabel');
        $exportUrl = $this->getControlAttribute('dropdown', 'export_to', 'selectedValue');
        $report = $this->getFile($exportUrl);
        if (strpos(strtolower($exportLabel), 'csv') !== false) {
            return $this->_csvToArray($report);
        }
        if (strpos(strtolower($exportLabel), 'excel') !== false) {
            $xmlArray = $this->_xmlToArray($report);
            return $this->_convertXmlReport($xmlArray);
        }
        $this->fail('Wrong type of export file');
    }

    /**
     * Converts CSV string to associative array
     *
     * @param string $input Input csv string to be converted to array
     * @param string $delimiter Delimiter
     *
     * @return array
     */
    protected function _csvToArray($input, $delimiter = ',')
    {
        return $this->csvHelper()->csvToArray($input, $delimiter);
    }

    /**
     * Converts XML string to associative array
     *
     * @param string $xmlString Input xml string to be converted to array
     *
     * @return array
     */
    protected function _xmlToArray($xmlString)
    {
        $xmlArray = json_decode(json_encode((array)simplexml_load_string($xmlString)), 1);
        return $xmlArray;
    }

    /**
     * Converts report xml array into readable associative array
     *
     * @param string $xmlArray Input array to be converted
     *
     * @return array
     */
    protected function _convertXmlReport($xmlArray)
    {
        $newXmlArray = $xmlArray['Worksheet']['Table']['Row'];
        $keys = array();
        $values = array();
        foreach ($newXmlArray[0]['Cell'] as $key => $value) {
            $keys[] = $newXmlArray[0]['Cell'][$key]['Data'];
        }
        for ($i = 1; $i < count($newXmlArray); $i++) {
            foreach ($newXmlArray[$i]['Cell'] as $key => $value) {
                $values[$i - 1][] = $newXmlArray[$i]['Cell'][$key]['Data'];
            }
        }
        $convertedXmlArray = array();
        foreach ($values as $key => $value) {
            $convertedXmlArray[] = array_combine($keys, $values[$key]);
        }
        return $convertedXmlArray;
    }

    /**
     * Verifies if exported report contains correct data
     *
     * @param array $expectedData Data expected to be in exported file
     * @param array $exportedData Exported report from csv or xml file
     *
     * @return void
     */
    public function verifyExportedReport(array $expectedData, array $exportedData)
    {
        $dataMatch = array();
        foreach ($expectedData as $key => $dataRow) {
            $dataMatch[] = array_intersect_assoc($exportedData[$key], $dataRow);
        }
        $this->assertEquals($dataMatch, $expectedData, 'Report was not exported correctly');
    }
}