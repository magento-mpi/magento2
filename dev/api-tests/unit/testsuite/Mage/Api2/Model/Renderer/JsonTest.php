<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test response renderer JSON adapter
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Renderer_JsonTest extends Mage_PHPUnit_TestCase
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
        /** @var $adapter Mage_Webapi_Model_Renderer_Json */
        $adapter = Mage::getModel('Mage_Webapi_Model_Renderer_Json');

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
