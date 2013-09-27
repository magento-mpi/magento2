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
        $objectManager = $this->getMock('Magento\ObjectManager');
        $objectManager->expects($this->once())
            ->method('get')
            ->with($this->stringContains('Magento\Core\Helper\Data'))
            ->will($this->returnValue($this->getMock('Magento\Core\Helper\Data', array(), array(), '', false, false)));
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
        return array(
            'string' => array('Magento\Core\Helper\Data'),
            'object' => array($this->getMock('Magento\Core\Helper\Data', array(), array(), '', false, false)),
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
