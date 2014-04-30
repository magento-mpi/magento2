<?php
/**
 * Test class for \Magento\Framework\Acl\ResourceFactory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Acl;

class ResourceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Acl\ResourceFactory
     */
    protected $_model;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Acl\Resource
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_objectManager = $this->getMockForAbstractClass(
            'Magento\Framework\ObjectManager',
            array(),
            '',
            true,
            true,
            true,
            array('create')
        );

        $this->_expectedObject = $this->getMock('Magento\Framework\Acl\Resource', array(), array(), '', false);

        $this->_model = $helper->getObject(
            'Magento\Framework\Acl\ResourceFactory',
            array('objectManager' => $this->_objectManager)
        );
    }

    public function testCreateResource()
    {
        $arguments = array('5', '6');
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Framework\Acl\Resource',
            $arguments
        )->will(
            $this->returnValue($this->_expectedObject)
        );
        $this->assertEquals($this->_expectedObject, $this->_model->createResource($arguments));
    }
}
