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
 * Test response renderer Query adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Renderer_QueryTest extends Mage_PHPUnit_TestCase
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
        /** @var $adapter Mage_Api2_Model_Renderer_Query */
        $adapter = Mage::getModel('api2/renderer_query');

        $this->assertEquals(
            $encoded, $adapter->render($decoded),
            'Decoded data is not like expected.');
    }

    /**
     * Provides data for testing successful flow
     *
     * @return array
     */
    public function dataProviderSuccess()
    {
        return array(
            array('0=assoc_item1&1=assoc_item2&assoc%3Atest001=%3Csome01%3Etext%3C%2Fsome01%3E&assoc.test002=1+%3E+'
                  . '0&assoc_test003.=chars+%5D%5D%3E&assoc_test004=chars++%21%22%23%24%25%26%27%28%29%2A%2B%2C%2F%3B%'
                  . '3C%3D%3E%3F%40%5B%5C%5D%5E%60%7B%7C%7D%7E++chars+&key+chars+%60%5C%2F%3B%3A%5D%5B%7B%7D%22%7C%27.%'
                  . '2C%7E%21%40%23%24%25%5E%26%2A%28%29_%2B=chars',
                array(
                    'assoc_item1',
                    'assoc_item2',
                    'assoc:test001' => '<some01>text</some01>',
                    'assoc.test002' => '1 > 0',
                    'assoc_test003.' => 'chars ]]>',
                    'assoc_test004' => 'chars  !"#$%&\'()*+,/;<=>?@[\]^`{|}~  chars ',
                    'key chars `\/;:][{}"|\'.,~!@#$%^&*()_+' => 'chars',
                )
            ),
            array(
                '0=test1&1=test2&2%5Btest01%5D=some1&2%5Btest02%5D=some2&2%5Btest03%5D%5Btest001%5D=some01&2%5Btest'
                .'03%5D%5Btest002%5D=some02',
                array(
                    'test1',
                    'test2',
                    (object) array(
                        'test01' => 'some1',
                        'test02' => 'some2',
                        'test03' => array(
                            'test001' => 'some01',
                            'test002' => 'some02',
                        ),
                    )
                )
            ),
            array('foo=', array('foo' => '')),
            array('foo_bar=', array('foo_bar' => '')),
            array('1=', array('1' => '')),
            array('1.234=0.123', array('1.234' => .123)),
            array('foo=bar', array('foo' => 'bar')),
            array('foo=%3Ebar', array('foo' => '>bar')),
            array('foo=bar%3D', array('foo' => 'bar=')),
            array('', array()),
            array('', new stdClass()),
        );
    }
}
