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
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Api2 router model
 */
class Mage_Api2_Model_RouterTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test not matched routes in Request
     *
     * @return void
     */
    public function testNotMatchedRoute()
    {
        /** @var $request Mage_Api2_Model_Request */
        $request = Mage::getSingleton('api2/request');
        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getSingleton('api2/router');

        try {
            $router->route($request);
        } catch (Mage_Api2_Exception $e) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $e->getCode());
            $this->assertEquals('Request not matched any route.', $e->getMessage());
            return;
        }

        $this->fail('An expected Mage_Api2_Exception has not been raised.');
    }
}
