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
 * LoadTest Session model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author     Victor Tihonchuk <victor@varien.com>
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
     * Init Session model
     *
     */
    public function __construct()
    {
        $this->init('loadtest');
        Mage::dispatchEvent('loadtest_session_init', array('loadtest_session'=>$this));

        $this->setCountLoadTime(0);
        if ($this->isEnabled()) {
            $this->_xml = new Varien_Simplexml_Element('<?xml version="1.0"?><loadtest></loadtest>');
            $this->_xml->addChild('response');
            $this->_getRequestUrl();
            $this->_getTotalData();
            $this->_getCacheSettings();
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
            'Mage_LoadTest_AuthController',
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
        return true;
        return $this->getKey() == Mage::getStoreConfig(self::XML_PATH_KEY);
    }

    public function getTemplateName($area, $templateName)
    {
        if ($this->isEnabled() && $this->isLoggedIn() && $area == 'frontend') {
            $templateName = preg_replace('/^page/', 'loadtest/page', $templateName);
        }
        return $templateName;
    }

    public function prepareXmlResponse($content)
    {
        Mage::app()->getResponse()->setHeader('Content-Type', 'text/xml');
        Mage::app()->getResponse()->setBody($content);
    }

    public function prepareOutputData()
    {
        /**
         * Prepare another data
         */
    }

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

        $totalCountsNode = $this->_xml->response->addChild('total_data_counts');
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

        if (!$this->_xml->request) {
            $this->_xml->addChild('request');
        }

        $requestUrl = Mage::app()->getRequest()->getOriginalPathInfo();
        $this->_xml->request->addChild('request_url', $requestUrl);

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
            'config'     => Mage::helper('adminhtml')->__('Configuration'),
            'layout'     => Mage::helper('adminhtml')->__('Layouts'),
            'block_html' => Mage::helper('adminhtml')->__('Blocks HTML output'),
            'eav'        => Mage::helper('adminhtml')->__('EAV types and attributes'),
            'translate'  => Mage::helper('adminhtml')->__('Translations'),
            'pear'       => Mage::helper('adminhtml')->__('PEAR Channels and Packages'),
        );

        $this->setCacheTypes($cacheTypes);

        $cacheNode = $this->_xml->response->addChild('cache');

        foreach ($cacheTypes as $type => $label) {
            $cacheNode->addChild($type, intval(Mage::app()->useCache($type)));
        }

        $this->setCountLoadTime($loadTime + (microtime(true) - $startTime));
    }
}