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
     * Returns row number(s) if found, or null otherwise.
     *
     * @param array $data Data to look for in report
     *
     * @return string|array|null
     */
    public function searchDataInReport(array $data)
    {
        $rowNumbers = array();
        $fieldsetLocator = $this->_getControlXpath('fieldset', 'report_tag_grid');
        list(, , $totalCount) = explode('|', $this->getElement($fieldsetLocator . "//td[@class='pager']")->text());
        $totalCount = trim(preg_replace('/[A-Za-z]+/', '', $totalCount));
        $xpathTR = $this->formSearchXpath($data);
        $availableElement = $this->elementIsPresent($xpathTR);
        if ($availableElement) {
            for ($i = 1; $i <= $totalCount; $i++) {
                if ($this->elementIsPresent(str_replace('tr', 'tr[' . $i . ']', $xpathTR))) {
                    $rowNumbers[] = $i;
                }
            }
            if (count($rowNumbers) == 1) {
                return $rowNumbers[0];
            } else {
                return $rowNumbers;
            }
        }
        return null;
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
            $this->assertEquals(
                $i + 1, $this->searchDataInReport($sortedReport[$i]), "Report sorting by $column is not correct");
        }
    }

    /**
     * Performs report export
     *
     * @return array|bool
     */
    public function export()
    {
        $exportUrl = $this->getControlAttribute('dropdown', 'export_to', 'selectedValue');
        $report = $this->getFile($exportUrl);
        if (strpos(strtolower($exportUrl), 'csv')) {
            return $this->_csvToArray($report);
        }
        if (strpos(strtolower($exportUrl), 'excel')) {
            $xmlArray = $this->_xmlToArray($report);
            return $this->_convertXmlReport($xmlArray);
        }
        return false;
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
        $temp = tmpfile();
        fwrite($temp, $input);
        fseek($temp, 0);
        $data = array();
        $header = array();
        while (($line = fgetcsv($temp, 10000, $delimiter, '"', '\\')) !== FALSE) {
            if (!$header) {
                $header = $line;
            } else {
                try {
                    $data[] = array_combine($header, $line);
                } catch (Exception $e) {
                    var_dump($e->getMessage());
                    return null;
                }
            }
        }
        return $data;
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