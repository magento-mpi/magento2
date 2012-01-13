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
 * Test Api2 Auth model
 */
class Mage_Api2_Model_AuthTest extends Magento_TestCase
{
    /**
     * Test authenticate method
     */
    public function testAuthenticate()
    {
        /** @var $request Mage_Api2_Model_Request */
        $request = Mage::getSingleton('api2/request');
        /** @var $authManager Mage_Api2_Model_Auth */
        $authManager = Mage::getModel('api2/auth');

        $apiUser = $authManager->authenticate($request);

        $this->assertInstanceOf('Mage_Api2_Model_Auth_User_Abstract', $apiUser);
    }
}
