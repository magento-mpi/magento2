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
 * Test response renderer XML adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Renderer_XmlTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test render data
     *
     * @dataProvider dataProviderSuccess
     * @param string $encoded
     * @param array|string|float|int|bool $decoded
     */
    public function testRenderData($encoded, $decoded)
    {
        /** @var $adapter Mage_Api2_Model_Renderer_Xml */
        $adapter = Mage::getModel('api2/renderer_xml');

        $xml = $adapter->render($decoded);
        $simpleXml = new SimpleXMLElement($xml);
        $this->assertInstanceOf('SimpleXMLElement', $simpleXml);
        $this->assertEquals($encoded, $xml,
            'Decoded data is not like expected.');
    }

    /**
     * Provides data for testing successful flow
     *
     * @return array
     */
    public function dataProviderSuccess()
    {
        return require dirname(__FILE__) . '/_fixtures/xmlDataProviderSuccessTest.php';
    }
}
