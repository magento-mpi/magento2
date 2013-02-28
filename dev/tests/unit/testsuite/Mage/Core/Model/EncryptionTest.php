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
        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->once())
            ->method('get')
            ->with($this->stringContains('Mage_Core_Helper_Data'))
            ->will($this->returnValue($this->getMock('Mage_Core_Helper_Data', array(), array(), '', false, false)));

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
            'object' => array($this->getMock('Mage_Core_Helper_Data', array(), array(), '', false, false)),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetHelperException()
    {
        $objectManager = $this->getMock('Magento_ObjectManager');
        /**
         * @var Mage_Core_Model_Encryption
         */
        $model = new Mage_Core_Model_Encryption($objectManager);
        /** Mock object is not instance of Mage_Code_Helper_Data and should not pass validation */
        $input = $this->getMock('Mage_Code_Helper_Data', array(), array(), '', false);
        $model->setHelper($input);
    }
}
