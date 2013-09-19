<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento;

class MageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Variable value before test
     *
     * @var bool
     */
    protected $_isSerializable;

    protected function setUp()
    {
        $this->_isSerializable = \Mage::getIsSerializable();
    }

    protected function tearDown()
    {
        \Mage::setIsSerializable($this->_isSerializable);
    }

    public function testSetGetSerializable()
    {
        $this->assertClassHasStaticAttribute('_isSerializable', 'Mage');

        \Mage::setIsSerializable(false);
        $this->assertFalse(\Mage::getIsSerializable());

        \Mage::setIsSerializable(true);
        $this->assertTrue(\Mage::getIsSerializable());

        // incorrect data
        \Mage::setIsSerializable('random_string');
        $this->assertTrue(\Mage::getIsSerializable());
    }
}
