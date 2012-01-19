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
class Mage_Api2_Model_RendererTest extends Magento_TestCase
{
    /**
     * Test response content renderer factory
     */
    public function testFactory()
    {
        $data = array(
            '*/*'               => 'Mage_Api2_Model_Renderer_Json',
            'application/*'     => 'Mage_Api2_Model_Renderer_Json',
            'application/json'  => 'Mage_Api2_Model_Renderer_Json',
            'application/xml'   => 'Mage_Api2_Model_Renderer_Xml',
            'text/plain'        => 'Mage_Api2_Model_Renderer_Query',
            'text/html'         => 'Mage_Api2_Model_Renderer_Html',
        );
        foreach ($data as $type => $expectedClass) {
            $adapter = Mage_Api2_Model_Renderer::factory($type);
            $this->assertInstanceOf($expectedClass, $adapter);
        }
    }

    /**
     * Test response content renderer factory with unknown accept type
     *
     * @expectedException Mage_Api2_Exception
     */
    public function testFactoryBadAcceptType()
    {
        /**
         * Try get adapter via invalid content type
         * and must be throw exception
         */
        Mage_Api2_Model_Renderer::factory('unknown/unknown');
    }
}
