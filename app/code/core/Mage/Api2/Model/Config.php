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
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api2 config model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Config extends Varien_Simplexml_Config
{
    /**
     * Id for config cache
     */
    const CACHE_ID  = 'config_api2';

    /**
     * Tag name for config cache
     */
    const CACHE_TAG = 'CONFIG_API2';

    /**
     * Constructor
     * Initializes XML for this configuration
     * Local cache configuration
     *
     * @param string|Varien_Simplexml_Element $sourceData
     */
    public function __construct($sourceData = null)
    {
        parent::__construct($sourceData);

        $canUserCache = Mage::app()->useCache('config');
        if ($canUserCache) {
            $this->setCacheId(self::CACHE_ID)
                ->setCacheTags(array(self::CACHE_TAG))
                ->setCacheChecksum(null)
                ->setCache(Mage::app()->getCache());

            if ($this->loadCache()) {
                return;
            }
        }

        // Load data of config files api2.xml
        $config = Mage::getConfig()->loadModulesConfiguration('api2.xml');
        $this->setXml($config->getNode('api2'));

        if ($canUserCache) {
            $this->saveCache();
        }
    }

    /**
     * Fetch all routes of the given api type from config files api2.xml
     *
     * @param string $apiType
     * @throws Mage_Api2_Exception
     * @return array
     */
    public function getRoutes($apiType)
    {
        /** @var $helper Mage_Api2_Helper_Data */
        $helper = Mage::helper('api2');
        if ($helper->isApiTypeExist($apiType)) {
            $routes = array();
            foreach ($this->getResources() as $resource) {
                if (!$resource->routes) {
                    continue;
                }

                foreach ($resource->routes->children() as $route) {
                    $arguments = array(
                        Mage_Api2_Model_Route_Abstract::PARAM_ROUTE    => (string)$route->mask,
                        Mage_Api2_Model_Route_Abstract::PARAM_DEFAULTS => array(
                            'model' => (string) $resource->model,
                            'type'  => (string) $resource->type,
                        )
                    );

                    $routes[] = Mage::getModel('api2/route_' . $apiType, $arguments);
                }
            }
            return $routes;
        } else {
            throw new Mage_Api2_Exception(sprintf('Invalid API type "%s".', $apiType),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Retrieve all resources from config files api2.xml
     *
     * @return Varien_Simplexml_Element|false
     */
    public function getResources()
    {
        return $this->getNode('resources')->children();
    }

    /**
     * Retrieve resource by type (node)
     *
     * @param string $node
     * @return Varien_Simplexml_Element|false
     */
    public function getResource($node)
    {
        return $this->getNode('resources/' . $node);
    }

    /**
     * Retrieve resource main route
     *
     * @param string $node
     * @return string
     */
    public function getMainRoute($node)
    {
        return (string) $this->getNode(join('/', array('resources', $node, 'routes', 'route_main', 'mask')));
    }
}
