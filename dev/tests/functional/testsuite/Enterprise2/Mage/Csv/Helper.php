<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Csv
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Csv Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Csv_Helper extends Mage_Selenium_TestCase
{
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
                    print_r($e);
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
        foreach ($input as $line) {
            if (!$header) {
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