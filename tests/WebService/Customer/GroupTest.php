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
 * @package    Mage_Tests
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (!defined('_IS_INCLUDED')) {
    require dirname(__FILE__) . '/../../PHPUnitTestInit.php';
    PHPUnitTestInit::runMe(__FILE__);
}

class WebService_Customer_GroupTest extends WebService_TestCase_Abstract
{
    /**
     * @dataProvider connectorProvider
     */
    public function testListIsNotEmpty(WebService_Connector_Interface $connector)
    {
        $result = $connector->call('customer_group.list');
        $this->assertTrue(isset($result[0]));
    }

    /**
     * @dataProvider connectorProvider
     */
    public function testListIsArray(WebService_Connector_Interface $connector)
    {
        $result = $connector->call('customer_group.list');
        $this->assertType('array', $result);
    }

    /**
     * @dataProvider connectorProvider
     */
    public function testListHasDefaultEntry(WebService_Connector_Interface $connector)
    {
        $result = $connector->call('customer_group.list');
        $this->assertTrue($result[0]['customer_group_id'] == 0);
    }
}
