<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Helper;

class ObjectManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * List of block default dependencies
     *
     * @var array
     */
    protected $_blockDependencies = array(
        'request'         => 'Magento\App\RequestInterface',
        'layout'          => 'Magento\View\LayoutInterface',
        'eventManager'    => 'Magento\Event\ManagerInterface',
        'translator'      => 'Magento\Core\Model\Translate',
        'cache'           => 'Magento\App\CacheInterface',
        'design'          => 'Magento\View\DesignInterface',
        'session'         => 'Magento\Core\Model\Session',
        'storeConfig'     => 'Magento\Core\Model\Store\Config',
        'frontController' => 'Magento\App\FrontController'
    );

    /**
     * List of model default dependencies
     *
     * @var array
     */
    protected $_modelDependencies = array(
        'eventDispatcher'    => 'Magento\Event\ManagerInterface',
        'cacheManager'       => 'Magento\App\CacheInterface',
        'resource'           => 'Magento\Core\Model\Resource\AbstractResource',
        'resourceCollection' => 'Magento\Data\Collection\Db'
    );

    /**
     * @covers \Magento\TestFramework\TestCase\ObjectManager::getBlock
     */
    public function testGetBlock()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var $template \Magento\Core\Block\Template */
        $template = $objectManager->getObject('Magento\Core\Block\Template');
        $this->assertInstanceOf('Magento\Core\Block\Template', $template);
        foreach ($this->_blockDependencies as $propertyName => $propertyType) {
            $this->assertAttributeInstanceOf($propertyType, '_' . $propertyName, $template);
        }

        $area = 'frontend';
        /** @var $appStateMock \Magento\App\State|\PHPUnit_Framework_MockObject_MockObject */
        $appStateMock = $this->getMock('Magento\App\State', array('getAreaCode'), array(), '', false);
        $appStateMock->expects($this->once())->method('getAreaCode')->will($this->returnValue($area));

        $context = $objectManager->getObject('Magento\Core\Block\Template\Context');
        $appStateProperty = new \ReflectionProperty('Magento\Core\Block\Template\Context', '_appState');
        $appStateProperty->setAccessible(true);
        $appStateProperty->setValue($context, $appStateMock);

        /** @var $template \Magento\Core\Block\Template */
        $template = $objectManager->getObject('Magento\Core\Block\Template', array('context' => $context));
        $this->assertEquals($area, $template->getArea());
    }

    /**
     * @covers \Magento\TestFramework\ObjectManager::getModel
     */
    public function testGetModel()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var $model \Magento\Core\Model\Config\Value */
        $model = $objectManager->getObject('Magento\Core\Model\Config\Value');
        $this->assertInstanceOf('Magento\Core\Model\Config\Value', $model);
        foreach ($this->_modelDependencies as $propertyName => $propertyType) {
            $this->assertAttributeInstanceOf($propertyType, '_' . $propertyName, $model);
        }

        /** @var $resourceMock \Magento\Core\Model\Resource\Resource */
        $resourceMock = $this->getMock(
            'Magento\Core\Model\Resource\Resource',
            array('_getReadAdapter', 'getIdFieldName', '__sleep', '__wakeup'),
            array(),
            '',
            false
        );
        $resourceMock->expects($this->once())
            ->method('_getReadAdapter')
            ->will($this->returnValue(false));
        $resourceMock->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('id'));
        $arguments = array('resource' => $resourceMock);
        $model = $objectManager->getObject('Magento\Core\Model\Config\Value', $arguments);
        $this->assertFalse($model->getResource()->getDataVersion('test'));
    }
}
