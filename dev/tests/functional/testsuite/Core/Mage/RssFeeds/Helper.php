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
class Core_Mage_RssFeeds_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Get rss by link element
     *
     * @param $rssControlType
     * @param $rssControl
     *
     * @return string
     */
    public function getRssFeeds($rssControlType, $rssControl)
    {
        $url = $this->getControlAttribute($rssControlType, $rssControl, 'href');
        return $this->getFile($url);
    }

    /**
     * Get rss items
     *
     * @param string $rssXmlString
     *
     * @return array
     */
    public function getRssItems($rssXmlString)
    {
        $xml = simplexml_load_string($rssXmlString);
        $result = $xml->xpath('/rss/channel/item');
        //combine to array
        $data = array();
        /**
         * @var SimpleXMLElement $item
         */
        foreach ($result as $item) {
            $dataRow = array();
            foreach ($item->children() as $key => $value) {
                $dataRow[$key] = (string)$value;
            }
            $data[] = $dataRow;
            unset($dataRow);
        }
        return $data;
    }
}
