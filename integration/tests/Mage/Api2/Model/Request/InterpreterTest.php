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
 * Request content interpreter factory
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Request_InterpreterTest extends Magento_TestCase
{
    /**
     * Test request content interpreter factory
     */
    public function testFactoryInputTypes()
    {
        $data = array(
            'application/xml'   => 'api2/request_interpreter_xml',
            'application/json'  => 'api2/request_interpreter_json',
            'text/plain'        => 'api2/request_interpreter_query',
        );
        foreach ($data as $type => $expectedClass) {
            $interpreter = Mage_Api2_Model_Request_Interpreter::factory($type);
            $this->assertInstanceOf($expectedClass, $interpreter);
        }
    }
}
