<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Csv Helper class
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Csv_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Convert CSV string to associative array
     *
     * @param string $input Input csv string to be converted to array
     * @param string $delimiter Delimiter
     *
     * @return array
     */
    public function csvToArray($input, $delimiter = ',')
    {
        $temp = tmpfile();
        fwrite($temp, $input);
        fseek($temp, 0);
        $data = array();
        $header = array();
        while (($line = fgetcsv($temp, 10000, $delimiter, '"', '\\')) !== false) {
            if ($header) {
                if (count($header) !== count($line)) {
                    $this->fail("Both parameters should have an equal number of elements\n" . print_r($input, true));
                }
                $data[] = array_combine($header, $line);
            } else {
                $header = $line;
            }
        }
        return $data;
    }

    /**
     * Convert associative array to CSV string
     *
     * @param array $input Input associative array to be converted to string
     * @param string $delimiter Delimiter
     *
     * @return string
     */
    public function arrayToCsv(array $input, $delimiter = ',')
    {
        $temp = tmpfile();
        $header = null;
        foreach ($input as $line) {
            if (!is_array($line)) {
                $this->fail('Parameter is not array.' . print_r($line, true) . "\n" . print_r($input, true));
            }
            if ($header === null) {
                $header = array_keys($line);
                fputcsv($temp, $header, $delimiter, '"');
            }
            fputcsv($temp, array_values($line), $delimiter, '"');
        }
        fseek($temp, 0);
        $csv = '';
        while (!feof($temp)) {
            $csv .= fread($temp, 1000);
        }
        return $csv;
    }
}