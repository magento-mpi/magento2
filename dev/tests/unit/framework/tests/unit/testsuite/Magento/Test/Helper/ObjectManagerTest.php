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

namespace Magento\Test\Helper;

class ObjectManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * List of block default dependencies
     *
     * @var array
     */
    protected $_blockDependencies = array(
        'request'         => 'Magento\Core\Controller\Request\Http',
        'layout'          => 'Magento\Core\Model\Layout',
        'eventManager'    => 'Magento\Core\Model\Event\Manager',
        'translator'      => 'Magento\Core\Model\Translate',
        'cache'           => 'Magento\Core\Model\CacheInterface',
        'design'   => 'Magento\Core\Model\View\DesignInterface',
        'session'         => 'Magento\Core\Model\Session',
        'storeConfig'     => 'Magento\Core\Model\Store\Config',
        'frontController' => 'Magento\Core\Controller\Varien\Front'
    );

    /**
     * List of model default dependencies
     *
     * @var array
     */
    protected $_modelDependencies = array(
        'eventDispatcher'    => 'Magento\Core\Model\Event\Manager',
        'cacheManager'       => 'Magento\Core\Model\CacheInterface',
        'resource'           => 'Magento\Core\Model\Resource\AbstractResource',
        'resourceCollection' => 'Magento\Data\Collection\Db'
    );

    /**
     * @covers Magento_TestFramework_TestCase_ObjectManager::getBlock
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
        /** @var $layoutMock \Magento\Core\Model\Layout */
        $layoutMock = $this->getMock('Magento\Core\Model\Layout', array('getArea'), array(), '', false);
        $layoutMock->expects($this->once())
            ->method('getArea')
            ->will($this->returnValue($area));

        $arguments = array('layout' => $layoutMock);
        /** @var $template \Magento\Core\Block\Template */
        $template = $objectManager->getObject('Magento\Core\Block\Template', $arguments);
        $this->assertEquals($area, $template->getArea());
    }

    /**
     * @covers Magento_TestFramework_ObjectManager::getModel
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
            array('_getReadAdapter', 'getIdFieldName'),
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
