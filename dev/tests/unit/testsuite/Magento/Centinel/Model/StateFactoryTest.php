<?php
/**
 * Magento_Centinel_Model_StateFactory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Centinel_Model_StateFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateState()
    {
        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->at(0))
            ->method('create')
            ->with('Magento_Centinel_Model_State_Visa')
            ->will($this->returnValue($this->getMock('Magento_Centinel_Model_State_Visa')));
        $objectManager->expects($this->at(1))
            ->method('create')
            ->with('Magento_Centinel_Model_State_Mastercard')
            ->will($this->returnValue($this->getMock('Magento_Centinel_Model_State_Mastercard')));
        $objectManager->expects($this->at(2))
            ->method('create')
            ->with('Magento_Centinel_Model_State_Jcb')
            ->will($this->returnValue($this->getMock('Magento_Centinel_Model_State_Jcb')));
        $objectManager->expects($this->at(3))
            ->method('create')
            ->with('Magento_Centinel_Model_State_Mastercard')
            ->will($this->returnValue($this->getMock('Magento_Centinel_Model_State_Mastercard')));

        $factory = new Magento_Centinel_Model_StateFactory(
            $objectManager,
            array(
                'VI'  => 'Magento_Centinel_Model_State_Visa',
                'MC'  => 'Magento_Centinel_Model_State_Mastercard',
                'JCB' => 'Magento_Centinel_Model_State_Jcb',
                'SM'  => 'Magento_Centinel_Model_State_Mastercard',
            )
        );
        $this->assertInstanceOf('Magento_Centinel_Model_State_Visa', $factory->createState('VI'));
        $this->assertInstanceOf('Magento_Centinel_Model_State_Mastercard', $factory->createState('MC'));
        $this->assertInstanceOf('Magento_Centinel_Model_State_Jcb', $factory->createState('JCB'));
        $this->assertInstanceOf('Magento_Centinel_Model_State_Mastercard', $factory->createState('SM'));
        $this->assertFalse($factory->createState('LOL'));
    }

    public function testCreateStateMapIsEmpty()
    {
        $objectManager = $this->getMock('Magento_ObjectManager');
        $factory = new Magento_Centinel_Model_StateFactory(
            $objectManager
        );
        $this->assertFalse($factory->createState('VI'));
        $this->assertFalse($factory->createState('MC'));
        $this->assertFalse($factory->createState('JCB'));
        $this->assertFalse($factory->createState('SM'));
        $this->assertFalse($factory->createState('LOL'));
    }
}