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
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest Renderer Abstract model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author     Victor Tihonchuk <victor@varien.com>
 */

abstract class Mage_LoadTest_Model_Renderer_Abstract extends Varien_Object
{
    /**
     * used memory collection array
     *
     * @var array
     */
    private $_usedMemory;

    /**
     * used memory index
     *
     * @var int
     */
    private $_usedMemoryIndex;

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
    public $debug = true;

    /**
     * Init model
     *
     */
    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '128M');

        $this->_usedMemory = array();
        $this->_usedMemoryIndex = 0;

        $this->_xml = new Varien_Simplexml_Element('<?xml version="1.0"?><loadtest></loadtest>');
    }

    public function getStores($storeIds = null)
    {
        $collection = Mage::getModel('core/store')
            ->getCollection();
        /* @var $collection Mage_Core_Model_Mysql4_Store_Collection */
        if (!is_null($storeIds)) {
            $collection->addIdFilter($stores);
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

    protected function _profilerOperationStart()
    {
        if ($this->debug) {
            $this->_operationData = array(
                'memory'    => memory_get_usage(),
                'time'      => microtime(true)
            );
        }
    }

    protected function _profilerOperationStop()
    {
        if ($this->debug) {
            $this->_operationData = array(
                'memory'        => memory_get_usage() - $this->_operationData['memory'],
                'time'          => microtime(true) - $this->_operationData['time'],
                'memory_usage'  => memory_get_usage(),
                'memory_real'   => memory_get_usage(true)
            );
        }
        $this->_operationCount ++;
    }

    protected function _profilerOperationAddDebugInfo($node)
    {
        $node->addChild('time', $this->_operationData['time']);
        $node->addChild('memory', $this->_operationData['memory']);
    }

    protected function _profilerAddChild($key, $value = null)
    {
        if (!$this->_xmlResponse->$key) {
            $this->_xmlResponse->addChild($key, $value);
        }
        else {
            $this->_xmlResponse->$key = $value;
        }
    }

    protected function _profilerEnd()
    {
        $this->_xmlResponse->addChild('operations_count', $this->_operationCount);
        $this->_xmlResponse->addChild('total_memory_usage', memory_get_usage());
        $this->_xmlResponse->addChild('total_real_memory_usage', memory_get_usage(true));
        $this->_xmlResponse->addChild('total_time', microtime(true) - $this->_profilerData['time']);
    }

    public function exception($text)
    {
        $this->_xmlResponse->addChild('exception', $text);
    }

    /**
     * Collect before operation used memory
     *
     */
    protected function _beforeUsedMemory()
    {
        $this->_usedMemoryIndex ++;
        $this->_usedMemory[$this->_usedMemoryIndex] = array(
            'before'        => memory_get_usage(),
            'after'         => memory_get_usage(),
            'before_time'   => microtime(true),
            'after_time'    => microtime(true)
        );
    }

    /**
     * Collect after operation used memory
     *
     */
    protected function _afterUsedMemory()
    {
        $this->_usedMemory[$this->_usedMemoryIndex]['after'] = memory_get_usage();
        $this->_usedMemory[$this->_usedMemoryIndex]['after_time'] = microtime(true);
    }

    /**
     * Get collected used memory
     * return array(after, before) in bytes
     *
     * @return array
     */
    public function getUsedMemory()
    {
        return $this->_usedMemory;
    }

    public function getResult()
    {
        return $this->_xml->asXML();
    }
}