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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Api2 router model
 */
class Mage_Api2_Model_RouterTest extends Magento_TestCase
{
    /**
     * Test routes match and set params to Request
     *
     * @return void
     */
    public function testRoute()
    {
        $baseUrl = strtr(Mage_Api2_Model_Request::BASE_URL, array(':api' => Mage_Api2_Model_Server::API_TYPE_REST));

        /** @var $request Mage_Api2_Model_Request */
        $request = Mage::getSingleton('api2/request');
        $request->setRequestUri($baseUrl . '/products/2')
            ->setBaseUrl($baseUrl);

        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getSingleton('api2/router');
        /** @var $config Mage_Api2_Model_Config */
        $config = Mage::getSingleton('api2/config');

        $this->assertNull($request->getParam('api'));
        $this->assertNull($request->getParam('id'));
        $this->assertNull($request->getParam('type'));
        $this->assertNull($request->getParam('model'));

        $router->setRoutes($config->getRoutes($request->getApiType()))
            ->route($request);

        $this->assertEquals(Mage_Api2_Model_Server::API_TYPE_REST, $request->getParam('api'));
        $this->assertEquals('2', $request->getParam('id'));
        $this->assertEquals('product', $request->getParam('type'));
        $this->assertEquals('Mage_Catalog_Model_Product_Api2', $request->getParam('model'));
    }
}
