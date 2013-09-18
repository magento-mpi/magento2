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

class Magento_TestFramework_Helper_ObjectManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of block default dependencies
     *
     * @var array
     */
    protected $_blockDependencies = array(
        'request'         => 'Magento_Core_Controller_Request_Http',
        'layout'          => 'Magento_Core_Model_Layout',
        'eventManager'    => 'Magento_Core_Model_Event_Manager',
        'translator'      => 'Magento_Core_Model_Translate',
        'cache'           => 'Magento_Core_Model_CacheInterface',
        'design'          => 'Magento_Core_Model_View_DesignInterface',
        'session'         => 'Magento_Core_Model_Session',
        'storeConfig'     => 'Magento_Core_Model_Store_Config',
        'frontController' => 'Magento_Core_Controller_Varien_Front'
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
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
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
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
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
