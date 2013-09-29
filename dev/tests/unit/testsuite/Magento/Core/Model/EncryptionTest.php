<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

class EncryptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider setHelperGetHashDataProvider
     */
    public function testSetHelperGetHash($input)
    {
        $helper = $this->getMockBuilder('Magento\Core\Helper\Data')
                      ->disableOriginalConstructor()
                      ->setMockClassName('Magento_Core_Helper_Data_Stub')
                      ->getMock();
        $objectManager = $this->getMock('Magento\ObjectManager');
        $objectManager->expects($this->once())
            ->method('get')
            ->with($this->stringContains('Magento_Core_Helper_Data_Stub'))
            ->will($this->returnValue($helper));
        $coreConfig = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false);

        /**
         * @var \Magento\Core\Model\Encryption
         */
        $model = new \Magento\Core\Model\Encryption($objectManager, $coreConfig, 'cryptKey');
        $model->setHelper($input);
        $model->getHash('password', 1);
    }

    /**
     * @return array
     */
    public function setHelperGetHashDataProvider()
    {
        $helper = $this->getMockBuilder('Magento\Core\Helper\Data')
                      ->disableOriginalConstructor()
                      ->setMockClassName('Magento_Core_Helper_Data_Stub')
                      ->getMock();
        return array(
            'string' => array('Magento_Core_Helper_Data_Stub'),
            'object' => array($helper),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetHelperException()
    {
        $objectManager = $this->getMock('Magento\ObjectManager');
        $coreConfig = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false);

        /**
         * @var \Magento\Core\Model\Encryption
         */
        $model = new \Magento\Core\Model\Encryption($objectManager, $coreConfig);
        /** Mock object is not instance of \Magento\Code\Helper\Data and should not pass validation */
        $input = $this->getMock('Magento\Code\Helper\Data', array(), array(), '', false);
        $model->setHelper($input);
    }
}
