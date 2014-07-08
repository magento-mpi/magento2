<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model\Handler\Controllers;

class ControllersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Logging\Model\Handler\Controllers
     */
    protected $_model;

    /**
     * @var \Magento\Logging\Model\Event
     */
    protected $_eventModel;

    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $objectManager->get('Magento\Logging\Model\Handler\Controllers');
        $this->_eventModel = $objectManager->get('\Magento\Logging\Model\Event');
        $this->_coreRegistry = $objectManager->get('\Magento\Framework\Registry');
    }

    public function testPostDispatchTaxClassSave()
    {
        $classType = 'PRODUCT';
        $config = [];
        $classModelMock = $this->getMock('\Magento\Tax\Model\ClassModel', [], [], '', false);
        $classModelMock->expects($this->once())->method('getClassType')->will($this->returnValue($classType));

        $this->_coreRegistry->register('tax_class_model', $classModelMock);

        $result = $this->_model->postDispatchTaxClassSave($config, $this->_eventModel);
        $this->assertEquals('tax_product_tax_classes', $result->getEventCode());
    }
}
