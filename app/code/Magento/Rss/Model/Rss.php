<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Auth session model
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Model_Rss
{
    protected $_feedArray = array();

    public function _addHeader($data = array())
    {
        $this->_feedArray = $data;
        return $this;
    }

    public function _addEntries($entries)
    {
        $this->_feedArray['entries'] = $entries;
        return $this;
    }

    public function _addEntry($entry)
    {
        $this->_feedArray['entries'][] = $entry;
        return $this;
    }

    public function getFeedArray()
    {
        return $this->_feedArray;
    }

    public function createRssXml()
    {
        try {
            $rssFeedFromArray = Zend_Feed::importArray($this->getFeedArray(), 'rss');
            return $rssFeedFromArray->saveXML();
        } catch (Exception $e) {
            return __('Error in processing xml. %1',$e->getMessage());
        }
    }
}
