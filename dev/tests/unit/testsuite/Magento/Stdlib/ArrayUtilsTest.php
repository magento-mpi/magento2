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

        $iterator = 0;
        foreach ($input as $value) {
            $iterator++;
            $this->assertEquals($iterator, $value);
        }
    }

    /**
     * Data provider for ksortMultibyteDataProvider
     * @todo implement provider with values which different depends on locale
     */
    public function ksortMultibyteDataProvider()
    {
        return array(array(array('б' => 2, 'в' => 3, 'а' => 1), 'ru_RU'));
    }

    /**
     * @covers \Magento\Stdlib\ArrayUtils::decorateArray
     */
    public function testDecorateArray()
    {
        $original = array(array('value' => 1), array('value' => 2), array('value' => 3));
        $decorated = array(
            array('value' => 1, 'is_first' => true, 'is_odd' => true),
            array('value' => 2, 'is_even' => true),
            array('value' => 3, 'is_last' => true, 'is_odd' => true)
        );

        // arrays
        $this->assertEquals($decorated, $this->_arrayUtils->decorateArray($original, ''));

        // \Magento\Object
        $sample = array(
            new \Magento\Object($original[0]),
            new \Magento\Object($original[1]),
            new \Magento\Object($original[2])
        );
        $decoratedVo = array(
            new \Magento\Object($decorated[0]),
            new \Magento\Object($decorated[1]),
            new \Magento\Object($decorated[2])
        );
        foreach ($decoratedVo as $obj) {
            $obj->setDataChanges(true); // hack for assertion
        }
        $this->assertEquals($decoratedVo, $this->_arrayUtils->decorateArray($sample, ''));
    }
}
