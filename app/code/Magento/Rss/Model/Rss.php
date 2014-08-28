<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Model;

/**
 * Auth session model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rss
{
    /**
     * @var array
     */
    protected $_feedArray = array();

    /**
     * @param array $data
     * @return $this
     * @codeCoverageIgnore
     */
    public function _addHeader($data = array())
    {
        $this->_feedArray = $data;
        return $this;
    }

    /**
     * @param array $entries
     * @return $this
     * @codeCoverageIgnore
     */
    public function _addEntries($entries)
    {
        $this->_feedArray['entries'] = $entries;
        return $this;
    }

    /**
     * @param array $entry
     * @return $this
     * @codeCoverageIgnore
     */
    public function _addEntry($entry)
    {
        $this->_feedArray['entries'][] = $entry;
        return $this;
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getFeedArray()
    {
        return $this->_feedArray;
    }

    /**
     * @return string
     */
    public function createRssXml()
    {
        try {
            $rssFeedFromArray = \Zend_Feed::importArray($this->getFeedArray(), 'rss');
            return $rssFeedFromArray->saveXML();
        } catch (\Exception $e) {
            return __('Error in processing xml. %1', $e->getMessage());
        }
    }
}
