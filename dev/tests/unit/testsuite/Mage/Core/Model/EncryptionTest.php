<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_EncryptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider setHelperGetHashDataProvider
     */
    public function testSetHelperGetHash($input)
    {
        $objectManager = $this->getMock('Magento_ObjectManager_Zend', array('get'), array(), '', false);
        $objectManager->expects($this->once())
            ->method('get')
            ->with('Mage_Core_Helper_Data')
            ->will($this->returnValue(new Mage_Core_Helper_Data()));

        /**
         * @var Mage_Core_Model_Encryption
         */
        $model = new Mage_Core_Model_Encryption($objectManager);
        $model->setHelper($input);
        $model->getHash('password', 1);
    }

    /**
     * @return array
     */
    public function setHelperGetHashDataProvider()
    {
        return array(
            'string' => array('Mage_Core_Helper_Data'),
            'object' => array(new Mage_Core_Helper_Data()),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetHelperException()
    {
        $objectManager = $this->getMock('Magento_ObjectManager_Zend', array(), array(), '', false);
        /**
         * @var Mage_Core_Model_Encryption
         */
        $model = new Mage_Core_Model_Encryption($objectManager);
        /** Mock object is not instance of Mage_Code_Helper_Data and should not pass validation */
        $input = $this->getMock('Mage_Code_Helper_Data', array(), array(), '', false);
        $model->setHelper($input);
    }
}
