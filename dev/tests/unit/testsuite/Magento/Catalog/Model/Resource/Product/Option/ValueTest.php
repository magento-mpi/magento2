<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Resource\Product\Option;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValueStub
     */
    protected $_object;

    /**
     * Option value title data
     *
     * @var array
     */
    public static $valueTitleData = array(
    'id'       => 2,
    'store_id' => \Magento\Catalog\Model\AbstractModel::DEFAULT_STORE_ID,
    'scope'    => array('title' => 1)
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
            $this->getMock('Magento\Core\Model\Context', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false),
            null,
            null,
            self::$valueTitleData
        );

        $this->_object->saveValueTitles($object);
    }
}
