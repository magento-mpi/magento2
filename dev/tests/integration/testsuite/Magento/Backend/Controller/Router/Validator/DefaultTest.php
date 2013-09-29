<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Controller\Router\Validator;

/**
 * @magentoAppArea adminhtml
 */
class DefaultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture global/areas/adminhtml/frontName 0
     * @expectedException \InvalidArgumentException
     * @magentoAppIsolation enabled
     */
    public function testConstructWithEmptyAreaFrontName()
    {
        $dataHelperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $dataHelperMock->expects($this->once())->method('getAreaFrontName')->will($this->returnValue(null));

        $options = array(
            'areaCode' => \Magento\Core\Model\App\Area::AREA_ADMINHTML,
            'baseController' => 'Magento\Backend\Controller\ActionAbstract',
            'backendData' => $dataHelperMock,
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Controller\Router\DefaultRouter', $options);
    }

    /**
     * @magentoConfigFixture global/areas/adminhtml/frontName backend
     * @magentoAppIsolation enabled
     */
    public function testConstructWithNotEmptyAreaFrontName()
    {
        $options = array(
            'areaCode'       => \Magento\Core\Model\App\Area::AREA_ADMINHTML,
            'baseController' => 'Magento\Backend\Controller\ActionAbstract',
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Controller\Router\DefaultRouter', $options);
    }
}
