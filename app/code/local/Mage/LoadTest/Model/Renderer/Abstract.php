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
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest Renderer Abstract model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */

abstract class Mage_LoadTest_Model_Renderer_Abstract extends Varien_Object
{
    /**
     * Memory limit on php ini (in bytes)
     *
     * @var int
     */
    protected $_memoryLimit;

    /**
     * Need a memory on operation (in bytes)
     *
     * @var int
     */
    protected $_memoryOnOperation = 2097152;

    /**
     * Call urls array
     *
     * @var array
     */
    protected $_urls = array();

    /**
     * profiler internal data
     *
     * @var array
     */
    protected $_profilerData;

    /**
     * operation internal data
     *
     * @var array
     */
    protected $_operationData;

    /**
     * Count operations
     *
     * @var int
     */
    protected $_operationCount = 0;

    /**
     * Simplexml Element
     *
     * @var Varien_Simplexml_Element
     */
    protected $_xml;

    /**
     * Request node
     *
     * @var Varien_Simplexml_Element
     */
    protected $_xmlRequest;

    /**
     * Response node
     *
     * @var Varien_Simplexml_Element
     */
    protected $_xmlResponse;

    /**
     * Field Set node
     *
     * @var Varien_Simplexml_Element
     */
    protected $_xmlFieldSet;

    /**
     * Debug and detail info
     *
     * @var bool
     */
    protected $_debug = false;

    /**
     * Init model
     *
     */
    public function __construct()
    {
        @set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $this->_xml = new Varien_Simplexml_Element('<?xml version="1.0"?><loadtest></loadtest>');
    }

    public function getStores($storeIds = null)
    {
        $collection = Mage::getModel('core/store')
            ->getCollection();
        /* @var $collection Mage_Core_Model_Mysql4_Store_Collection */
        if (!is_null($storeIds)) {
            $collection->addIdFilter($storeIds);
        }

        return $collection;
    }

    /**
     * Empty render method
     *
     * @return Mage_LoadTest_Model_Renderer_Abstract
     */
    public function render()
    {
        return $this;
    }

    /**
     * Empty delete method
     *
     * @return Mage_LoadTest_Model_Renderer_Abstract
     */
    public function delete()
    {
        return $this;
    }

    /**
     * Begin of profiler
     *
     */
    protected function _profilerBegin()
    {
        $this->_profilerData = array(
            'time'  => microtime(true)
        );

        /**
         * Request
         */
        if (!$this->_xml->request) {
            $this->_xml->addChild('request');
        }
        $this->_xmlRequest = $this->_xml->request;

        foreach ($this->getData() as $k => $v) {
            $this->_xmlRequest->addChild($k, $v);
        }

        /**
         * Default Response
         */
        if (!$this->_xml->response) {
            $this->_xml->addChild('response');
        }
        $this->_xmlResponse = $this->_xml->response;
    }

    /**
     * Profiler operation start
     *
     */
    protected function _profilerOperationStart()
    {
        $this->_operationData = array(
            'memory'    => $this->_getMemoryUsage(),
            'time'      => microtime(true)
        );
    }

    /**
     * Profiler operation stop
     *
     */
    protected function _profilerOperationStop()
    {
        $this->_operationData = array(
            'memory'        => $this->_getMemoryUsage() - $this->_operationData['memory'],
            'time'          => microtime(true) - $this->_operationData['time'],
            'memory_usage'  => $this->_getMemoryUsage(),
            'memory_real'   => $this->_getMemoryUsage(true)
        );

        if ($this->_memoryOnOperation < $this->_operationData['memory']) {
            $this->_memoryOnOperation = $this->_operationData['memory'] + 1024*1024;
        }

        $this->_operationCount ++;
    }

    /**
     * Add debug info to operation
     *
     * @param SimpleXMLElement $node
     */
    protected function _profilerOperationAddDebugInfo($node)
    {
        $node->addChild('time', $this->_operationData['time']);
        $node->addChild('memory', $this->_operationData['memory']);
    }

    /**
     * Add additional parameter to response
     *
     * @param string $key
     * @param mixed $value
     */
    protected function _profilerAddChild($key, $value = null)
    {
        if (!$this->_xmlResponse->$key) {
            $this->_xmlResponse->addChild($key, $value);
        }
        else {
            $this->_xmlResponse->$key = $value;
        }
    }

    /**
     * End of profiler
     *
     */
    protected function _profilerEnd()
    {
        $this->_xmlResponse->addChild('operations_count', $this->_operationCount);
        $this->_xmlResponse->addChild('total_memory_usage', $this->_getMemoryUsage());
        $this->_xmlResponse->addChild('total_real_memory_usage', $this->_getMemoryUsage(true));
        $this->_xmlResponse->addChild('total_time', microtime(true) - $this->_profilerData['time']);

        if ($this->_urls) {
            $fetchUrlsNode = $this->_xmlResponse->addChild('fetch_urls');
            foreach ($this->_urls as $url) {
            	$fetchUrlsNode->addChild('url', $url);
            }
            $this->_xmlResponse->addChild('memory_on_operation', $this->_memoryOnOperation);
        }
    }

    /**
     * Exception response
     *
     * @param string $text
     */
    public function exception($text)
    {
        $this->_xmlResponse->addChild('exception', $text);
        $this->_profilerEnd();
    }

    /**
     * Get memory limit on config (in bytes)
     *
     * @return int
     */
    protected function _getMemoryLimit()
    {
        if (is_null($this->_memoryLimit)) {
            if ($memoryLimit = ini_get('memory_limit')) {
                switch (strtolower(substr($memoryLimit, -1))) {
                    case 'g':
                        $this->_memoryLimit = intval($memoryLimit) * 1024 * 1024 * 1024;
                        break;
                    case 'm':
                        $this->_memoryLimit = intval($memoryLimit) * 1024 * 1024;
                        break;
                    case 'k':
                        $this->_memoryLimit = intval($memoryLimit) * 1024;
                        break;
                    default:
                        $this->_memoryLimit = intval($memoryLimit);
                }
            }
            else {
                $this->_memoryLimit = 8 * 1024 * 1024;
            }
        }
        return $this->_memoryLimit;
    }

    /**
     * Retrieve memory usage
     *
     * @param bool $realUsage
     * @return int
     */
    protected function _getMemoryUsage($realUsage = null)
    {
        if (function_exists('memory_get_usage')) {
            return memory_get_usage($realUsage);
        }
        else {
            return 0;
        }
    }

    /**
     * Check memory suffice for operation
     *
     * @return bool
     */
    protected function _checkMemorySuffice()
    {
        return $this->_getMemoryLimit() > ($this->_getMemoryUsage() + $this->_memoryOnOperation);
    }

    public function setDebug($flag)
    {
        $this->_debug = $flag;
        return $this;
    }

    public function getDebug()
    {
        return $this->_debug;
    }

    /**
     * Debug
     *
     * @param int|bool $value
     * @return Mage_LoadTest_Model_Renderer_Abstract
     */
    public function setDetailLog($value)
    {
        $this->setData('detail_log', intval($value));
        $this->setDebug((bool)$value);

        return $this;
    }

    /**
     * Get result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->_xml->asXML();
    }
}