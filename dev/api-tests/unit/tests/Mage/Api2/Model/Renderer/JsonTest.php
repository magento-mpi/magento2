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
 * Test response renderer JSON adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Renderer_JsonTest extends Mage_PHPUnit_TestCase
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
        /** @var $adapter Mage_Api2_Model_Renderer_Json */
        $adapter = Mage::getModel('api2/renderer_json');

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
            array('{"0":"assoc_item1","1":"assoc_item2","assoc:test001":"<some01>text<\\/some01>","assoc.test002":"1 > 0","assoc_test003.":"chars ]]>","assoc_test004":"chars  !\"#$%&\'()*+,\/;<=>?@[\\\]^`{|}~  chars ","key chars `\\\\\/;:][{}\"|\'.,~!@#$%^&*()_+":"chars"}',
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
                '{"key1":"test1","key2":"test2","array":{"test01":"some1","test02":"some2"}}',
                array(
                    'key1' => 'test1',
                    'key2' => 'test2',
                    'array' => (object) array(
                        'test01' => 'some1',
                        'test02' => 'some2',
                    )
                )),
            array('null', null),
            array('true', true),
            array('1', 1),
            array('1.234', 1.234),
        );
    }
}
