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
class Community2_Mage_Reports_Helper extends Mage_Selenium_TestCase
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
        $qtyElementsInTable = $this->_getControlXpath('pageelement', 'qtyElementsInTable');
        $totalCount = intval($this->getText($qtyElementsInTable));
        $xpathTR = $this->formSearchXpath($data);
        if ($this->isElementPresent($xpathTR)) {
            for ($i = 1; $i <= $totalCount; $i++) {
                if ($this->isElementPresent(str_replace('tr', 'tr[' . $i .']', $xpathTR))) {
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
        $currentSorting = array();
        $newSorting = array();
        for ($i = 0; $i < count($data); $i++) {
            $currentSorting[$i] = $data[$i][$column];
            $newSorting[$i] = $data[$i][$column];
        }
        sort($newSorting);
        $sortedReport = array();
        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < count($data); $j++) {
                if ($currentSorting[$i] && $newSorting[$j] && $currentSorting[$i] == $newSorting[$j]) {
                    $sortedReport[$j] = $data[$i];
                    unset($currentSorting[$i]);
                    unset($newSorting[$j]);
                }
            }
        }
        for ($i = 0; $i < count($sortedReport); $i++) {
            $this->assertEquals($i+1, $this->searchDataInReport($sortedReport[$i]),
                "Report sorting by $column is not correct");
        };
    }

    /**
     * Performs report export
     *
     * @return array|bool
     */
    public function export()
    {
        $exportUrl = $this->getSelectedValue($this->_getControlXpath('dropdown', 'export_to'));
        $report = $this->_getFile($exportUrl);
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
     * Get file from admin area
     * Suitable for export testing
     *
     * @param string $url Url to the file or submit form
     * @return string
     */
    protected function _getFile($url)
    {
        $cookie = $this->getCookie();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    /**
     * Converts CSV string to associative array
     *
     * @param string $input Input csv string to be converted to array
     * @param string $delimiter Delimiter
     * @return array
     */
    protected function _csvToArray($input, $delimiter = ',')
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
     * @return array
     */
    protected function _xmlToArray($xmlString)
    {
        $xmlArray = json_decode(json_encode((array) simplexml_load_string($xmlString)), 1);
        return $xmlArray;
    }

    /**
     * Converts report xml array into readable associative array
     *
     * @param string $xmlArray Input array to be converted
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
                $values[$i-1][] =  $newXmlArray[$i]['Cell'][$key]['Data'];
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
     * @return void
     */
    public function verifyExportedReport(array $expectedData, array $exportedData)
    {
        $dataMatch = array();
        foreach ($expectedData as $key => $dataRow) {
            $dataMatch[] = array_intersect_assoc($exportedData[$key], $dataRow);
        }
        $this->assertEquals($dataMatch, $expectedData,
            'Report was not exported correctly');
    }
}