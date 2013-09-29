<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Block\Widget\Grid\Column;

class MultistoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Multistore
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);

        $arguments = array(
            'storeManager' => $this->_storeManagerMock,
            'urlBuilder' => $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false)
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento\Backend\Block\Widget\Grid\Column\Multistore',
            $arguments);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_storeManagerMock);
    }

    public function testIsDisplayedReturnsTrueInMultiStoreMode()
    {
        $this->_storeManagerMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(false));
        $this->assertTrue($this->_model->isDisplayed());
    }

    public function testIsDisplayedReturnsFalseInSingleStoreMode()
    {
        $this->_storeManagerMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->assertFalse($this->_model->isDisplayed());
    }
}
