<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Option;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Option\ValueStub
     */
    protected $_object;

    /**
     * Option value title data
     *
     * @var array
     */
    public static $valueTitleData = array(
        'id' => 2,
        'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
        'scope' => array('title' => 1)
    );

    protected function setUp()
    {
        $this->_object = new \Magento\Catalog\Model\Resource\Product\Option\ValueStub();
    }

    protected function tearDown()
    {
        unset($this->_object);
    }

    /**
     * Test that there is no notice in _saveValueTitles()
     *
     * @covers \Magento\Catalog\Model\Resource\Product\Option\Value::_saveValueTitles
     */
    public function testSaveValueTitles()
    {
        $object = new Stub(
            $this->getMock('Magento\Framework\Model\Context', array(), array(), '', false),
            $this->getMock('Magento\Framework\Registry', array(), array(), '', false),
            null,
            null,
            self::$valueTitleData
        );

        $this->_object->saveValueTitles($object);
    }
}
