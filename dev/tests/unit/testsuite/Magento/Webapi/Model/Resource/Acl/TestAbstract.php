<?php
/**
 * Abstract test class for Magento_Webapi_Model_Resource_Acl. Added to eliminate copy-paste.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Resource\Acl;

class TestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_helper;

    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\Resource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var \Magento\DB\Adapter\Pdo\Mysql|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapter;

    protected function setUp()
    {
        $this->_helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();
    }
}
