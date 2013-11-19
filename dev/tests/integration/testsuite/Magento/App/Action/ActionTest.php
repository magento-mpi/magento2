<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Action;

/**
 * @magentoAppArea frontend
 */
class ActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Action\Action|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_objectManager->get('Magento\App\State')->setAreaCode(\Magento\Core\Model\App\Area::AREA_FRONTEND);
        $this->_objectManager->get('Magento\View\DesignInterface')
            ->setDefaultDesignTheme();
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\App\RequestInterface');
        $arguments = array(
            'request'  => $request,
            'response' => $this->_objectManager->get('Magento\TestFramework\Response'),
        );
        $this->_objectManager->get('Magento\View\DesignInterface')
            ->setDefaultDesignTheme();
        $context = $this->_objectManager->create('Magento\App\Action\Context', $arguments);
        $this->_object = $this->getMockForAbstractClass(
            'Magento\App\Action\Action',
            array('context' => $context)
        );
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\LayoutInterface');
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf('Magento\TestFramework\Request', $this->_object->getRequest());
    }

    public function testGetResponse()
    {
        $this->assertInstanceOf('Magento\TestFramework\Response', $this->_object->getResponse());
    }


    /**
     * @magentoAppIsolation enabled
     */
    public function testDispatch()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Can\' dispatch - headers already sent');
        }
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $request->setDispatched();

        $arguments = array(
            'request'  => $request,
            'response' => $this->_objectManager->get('Magento\TestFramework\Response'),
        );
        $context = $this->_objectManager->create('Magento\App\Action\Context', $arguments);

        /* Area-specific controller is used because area must be known at the moment of loading the design */
        $this->_object = $this->_objectManager->create(
            'Magento\App\Action\Action',
            array('context'  => $context)
        );
        $this->_objectManager->get('Magento\Config\ScopeInterface')->setCurrentScope('frontend');
        $this->_object->dispatch('not_exists');

        $this->assertFalse($request->isDispatched());
        $this->assertEquals('cms', $request->getModuleName());
        $this->assertEquals('index', $request->getControllerName());
        $this->assertEquals('noroute', $request->getActionName());
    }

    /**
     * @return array
     */
    public function controllerAreaDesignDataProvider()
    {
        return array(
            'install' => array(
                'Magento\Install\Controller\Action',
                'install',
                'default',
                'magento_basic',
                'Magento\App\Action\Context'
            ),
            'frontend' => array(
                'Magento\App\Action\Action',
                'frontend',
                'default',
                'magento_blank',
                'Magento\App\Action\Context'
            ),
            'backend' => array(
                'Magento\Backend\Controller\Adminhtml\Action',
                'adminhtml',
                'admin',
                'magento_backend',
                'Magento\Backend\App\Action\Context'
            ),
        );
    }
}
