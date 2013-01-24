<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Url_RewriteFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Bogus string to return from object manager's create() method
     */
    const CREATE_RESULT = 'create_result';

    /**
     * Expected class name
     */
    const EXPECTED_CLASS_NAME = 'Mage_Core_Model_Url_Rewrite';

    /**
     * @var array
     */
    protected $_arguments = array(
        'key' => 'value'
    );

    public function testCreateFromArray()
    {
        $objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with(self::EXPECTED_CLASS_NAME, $this->_arguments, false)
            ->will($this->returnValue(self::CREATE_RESULT));

        $factory = new Mage_Core_Model_Url_RewriteFactory($objectManagerMock);
        $this->assertEquals(self::CREATE_RESULT, $factory->createFromArray($this->_arguments));
    }
}
