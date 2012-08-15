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
class Community2_Mage_RssFeeds_Helper extends Mage_Selenium_TestCase
{
    /**
     * Get file from admin area
     *
     * @param string $urlPage Url to the file or submit form
     *
     * @return string
     */
    protected function _getFile($urlPage)
    {
        $cookie = $this->getCookie();
        $connect     = curl_init();
        //Open page and get content
        curl_setopt($connect, CURLOPT_URL, $urlPage);
        curl_setopt($connect, CURLOPT_HEADER, false);
        curl_setopt($connect, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connect, CURLOPT_COOKIE, $cookie);
        curl_setopt($connect, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($connect, CURLOPT_TIMEOUT, 120);
        $data = curl_exec($connect);
        curl_close($connect);
        //Return response
        return $data;
    }

    /**
     * Get rss xml by URL
     *
     * @param string $url Path to rss feeds
     *
     * @return string
     */
    public function getRssFeedsByUrl($url)
    {
        $data = $this->_getFile($url);
        return $data;
    }

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
        $xPath = $this->_getControlXpath($rssControlType, $rssControl);
        $url = $this->getAttribute($xPath . '@href');
        $data = $this->getRssFeedsByUrl($url);
        return $data;
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
        foreach ($result as $item) {
            foreach ($item->children() as $key => $value) {
                $dataRow[$key] = (string) $value;
            }
            $data[] = $dataRow;
            unset($dataRow);
        }
        return $data;
    }
}
