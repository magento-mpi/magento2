<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Stdlib;

/**
 * Magento\Stdlib\ArrayUtilsTest test case
 */
class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Stdlib\ArrayUtils
     */
    protected $_arrayUtils;

    protected function setUp()
    {
        $this->_arrayUtils = new ArrayUtils();
    }

    /**
     * @covers \Magento\Stdlib\ArrayUtils::ksortMultibyte
     * @dataProvider ksortMultibyteDataProvider
     */
    public function testKsortMultibyte($input, $locale)
    {
        $this->_arrayUtils->ksortMultibyte($input, $locale);

        $i = 0;
        foreach ($input as $value) {
            $i++;
            $this->assertEquals($i, $value);

        }
    }

    /**
     * Data provider for ksortMultibyteDataProvider
     * @todo implement provider with values which different depends on locale
     */
    public function ksortMultibyteDataProvider()
    {
        return array(
            array(array('б' => 2, 'в' => 3, 'а' => 1), 'ru_RU'),
        );
    }
}
