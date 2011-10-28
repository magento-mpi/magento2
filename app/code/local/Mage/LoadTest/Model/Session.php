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
 * LoadTest Session model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_LoadTest_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * XML path to auth key
     *
     */
    const XML_PATH_KEY      = 'dev/loadtest/key';

    /**
     * XML path to module status
     *
     */
    const XML_PATH_STATUS   = 'dev/loadtest/status';

    /**
     * SimpleXml
     *
     * @var Varien_Simplexml_Element
     */
    protected $_xml;

    /**
     * SimpleXml request node
     *
     * @var Varien_Simplexml_Element
     */
    protected $_xml_request;

    /**
     * SimpleXml response node
     *
     * @var Varien_Simplexml_Element
     */
    protected $_xml_response;

    protected $_blocks = array();
    protected $_layouts = array();

    protected $_timers = array();
    protected $_paths = array();

    /**
     * Init Session model
     *
     */
    public function __construct()
    {
        $this->init('loadtest');

        $this->setCountLoadTime(0);
        if ($this->isEnabled()) {
            $this->_xml = new Varien_Simplexml_Element('<?xml version="1.0"?><loadtest></loadtest>');
            $this->_xml_request = $this->_xml->addChild('request');
            $this->_xml_response = $this->_xml->addChild('response');
            $this->_getRequestUrl();
            $this->_getTotalData();
            $this->_getCacheSettings();

            $this->_timers['page'] = null;
            $this->_timers['block'] = array();
            $this->_timers['sql'] = array();
        }
    }

    /**
     * Return status module
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_STATUS);
    }

    public function isAcceptedController($controllerName)
    {
        $controllers = array(
            'Mage_LoadTest_IndexController',
            'Mage_LoadTest_RenderController',
            'Mage_LoadTest_DeleteController',
        );

        return !in_array($controllerName, $controllers);
    }

    public function spiderXml()
    {
        $this->_xml = new Varien_Simplexml_Element('<?xml version="1.0"?><loadtest></loadtest>');
        $this->_xml->addChild('status', intval($this->isEnabled()));
        $this->_xml->addChild('logged_in', intval($this->isLoggedIn()));
    }

    public function login($key)
    {
        $this->setKey($key);
        return $this->isLoggedIn();
    }

    /**
     * Return authorization status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->getKey() == Mage::getStoreConfig(self::XML_PATH_KEY);
    }

    public function isToProcess()
    {
        return true;
        return $this->isEnabled() && $this->isLoggedIn();
    }

    public function pageStart()
    {
        $this->_timers['page'] = microtime(true);
    }

    public function pageStop()
    {
        if ($this->_timers['page']) {
            if (!$this->_xml_response->page) {
                $this->_xml_response->addChild('page');
            }
            $this->_xml_response->page->addChild('total_time', microtime(true) - $this->_timers['page']);
        }
    }

    public function getBlockPath($block, $path = array())
    {
        $path[] = $block->getNameInLayout();
        if ($block->getParentBlock()) {
            $blockPath = $this->getBlockPath($block->getParentBlock(), $path);
        }
        else {
            $blockPath = '/' . join('/', array_reverse($path));
        }
        if (!isset($this->_paths[$block->getNameInLayout()])) {
            $this->_paths[$block->getNameInLayout()] = $blockPath;
        }
        return $blockPath;
    }

    public function blockStart($path, $useLayout = false)
    {
$useLayout=0;
        if (isset($this->_blocks[$path])) {
            $this->_blocks[$path] ++;
        }
        else {
            $this->_blocks[$path] = 1;
            $this->_timers['block'][$path] = array();
        }
        $this->_timers['block'][$path][$this->_blocks[$path]] = array(
            microtime(true),
            microtime(true),
            $useLayout ? $this->_layouts[$path][1] - $this->_layouts[$path][0] : 0
        );
    }

    public function blockStop($path)
    {
        if (isset($this->_blocks[$path]) && isset($this->_timers['block'][$path][$this->_blocks[$path]])) {
            $this->_timers['block'][$path][$this->_blocks[$path]][1] = microtime(true);
        }
    }

    public function layoutStart($path)
    {
        $this->_layouts[$path] = array(microtime(true), microtime(true));
    }

    public function layoutStop($path)
    {
        if (isset($this->_layouts[$path])) {
            $this->_layouts[$path][1] = microtime(true);
        }
    }

    public function prepareXmlResponse($content)
    {
        Mage::app()->getResponse()->setHeader('Content-Type', 'text/xml');
        Mage::app()->getResponse()->setBody($content);
    }

    public function prepareOutputData()
    {
        /**
         * Prepare SQL data
         */

//        $sqlNode = $this->_xml_response->addChild('sql');
//        $sqlNode->addChild('total_time', $this->_sql_total_time);
//        $queriesNode = $sqlNode->addChild('queries');
//
//        arsort($this->_sql);
//
//        foreach ($this->_sql as $sql => $count) {
//            $queryNode = $queriesNode->addChild('query');
//            $queryNode->addChild('string', $sql)
//                ->addAttribute('count', $count);
//            $i = 0;
//            foreach ($this->_timers['sql'][$sql] as $timer) {
//                $queryNode->addChild('time', $timer[1] - $timer[0])
//                    ->addAttribute('id', $i);
//                $i ++;
//            }
//        }

        if ($profilers = Mage::registry('loadtest_db_profilers')) {
            $totalSqlTime = 0;
            $totalSqlCount = 0;

            $sqlNode = $this->_xml_response->addChild('sql');

            foreach ($profilers->getData() as $profiler) {
                /* @var $profiler Mage_LoadTest_Model_Db_Profiler */
                foreach ($profiler->getQueryProfiles() as $queryId => $profilerQuery) {
                    /* @var $profilerQuery Zend_Db_Profiler_Query */

                    $sqlQueryNode = $sqlNode->addChild('sql', $profilerQuery->getQuery());
                    $sqlQueryNode->addAttribute('time', $profilerQuery->getElapsedSecs());
                    $sqlQueryNode->addAttribute('params', serialize($profilerQuery->getQueryParams()));
                    $sqlQueryNode->addAttribute('type', $profilerQuery->getQueryType());

                    $totalSqlCount ++;
                    $sqlQueryNode->addChild('trace', $profiler->getTrace($queryId));
                }

                $totalSqlTime += $profiler->getTotalElapsedSecs();
            }

            $sqlNode->addChild('total_time', $totalSqlTime);
            $sqlNode->addChild('total_count', $totalSqlCount);
        }

        /**
         * Prepare block data
         */
        $blocksNode = $this->_xml_response->addChild('blocks');

        ksort($this->_blocks);

        $totalLayoutTime = 0;

        foreach ($this->_blocks as $blockName => $count) {
            $blockNode = $blocksNode->addChild('block');
            $blockNode->addChild('name', $blockName)
                ->addAttribute('count', $count);
            if (isset($this->_paths[$blockName])) {
                $blockNode->addChild('path', $this->_paths[$blockName]);
            }

            $i = 0;
            foreach ($this->_timers['block'][$blockName] as $timer) {
                $blockNode->addChild('to_html_time', ($timer[1] - $timer[0]))
                    ->addAttribute('id', $i);
                $blockNode->addChild('prepare_layout_time', $timer[2])
                    ->addAttribute('id', $i);
                $totalLayoutTime += $timer[2];
                $i ++;
            }
        }

        $blocksNode->addChild('total_prepare_layout_time', $totalLayoutTime);
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

    /**
     * Get Total Data counts
     *
     */
    protected function _getTotalData()
    {
        $loadTime  = $this->getCountLoadTime();
        $startTime = microtime(true);

        $categoriesCount = Mage::getModel('catalog/category')
            ->getCollection()
            ->getSize();

        $productsCount = Mage::getModel('catalog/product')
            ->getCollection()
            ->getSize();

        $customersCount = Mage::getModel('customer/customer')
            ->getCollection()
            ->getSize();

        $ordersCount = Mage::getModel('sales/order')
            ->getCollection()
            ->getSize();

        $tagsCount = Mage::getModel('tag/tag')
            ->getCollection()
            ->getSize();

        $reviewsCount = Mage::getModel('review/review')
            ->getCollection()
            ->getSize();

        $totalCountsNode = $this->_xml_response->addChild('total_data_counts');
        $totalCountsNode->addChild('products', $productsCount);
        $totalCountsNode->addChild('categories', $categoriesCount);
        $totalCountsNode->addChild('customers', $customersCount);
        $totalCountsNode->addChild('orders', $ordersCount);
        $totalCountsNode->addChild('tags', $tagsCount);
        $totalCountsNode->addChild('reviews', $reviewsCount);

        $this->setCountLoadTime($loadTime + (microtime(true) - $startTime));
    }

    /**
     * Get Request URL
     *
     */
    protected function _getRequestUrl()
    {
        $loadTime  = $this->getCountLoadTime();
        $startTime = microtime(true);

        $requestUrl = serialize(Mage::app()->getRequest()->getParams());

        $this->_xml_request->addChild('request_url', $requestUrl);

        $this->setCountLoadTime($loadTime + (microtime(true) - $startTime));
    }

    /**
     * Get Cache settings (for each type of caches)
     *
     */
    protected function _getCacheSettings()
    {
        $loadTime  = $this->getCountLoadTime();
        $startTime = microtime(true);

        $cacheTypes = array(
            'config'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Configuration'),
            'layout'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Layouts'),
            'block_html' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Blocks HTML output'),
            'eav'        => Mage::helper('Mage_Adminhtml_Helper_Data')->__('EAV types and attributes'),
            'translate'  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Translations'),
            'pear'       => Mage::helper('Mage_Adminhtml_Helper_Data')->__('PEAR Channels and Packages'),
        );

        $this->setCacheTypes($cacheTypes);

        $cacheNode = $this->_xml_response->addChild('cache');

        foreach ($cacheTypes as $type => $label) {
            $cacheNode->addChild($type, intval(Mage::app()->useCache($type)));
        }

        $this->setCountLoadTime($loadTime + (microtime(true) - $startTime));
    }
}