<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @magentoAppArea frontend
 */
class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\App\FrontController
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create('Magento\App\FrontController');
    }

    public function testGetRequest()
    {
        $this->assertNull($this->_model->getRequest());
    }

    public function testGetResponse()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')->setResponse(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\App\ResponseInterface')
        );
        if (!\Magento\TestFramework\Helper\Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test get response without sending headers');
        }
        $this->assertInstanceOf('Magento\App\ResponseInterface', $this->_model->getResponse());
    }

    public function testDispatch()
    {
        if (!\Magento\TestFramework\Helper\Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Cant\'t test dispatch process without sending headers');
        }
        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->_objectManager->get('Magento\App\State')->setAreaCode('frontend');
        $request = $this->_objectManager->create('Magento\App\Request\Http');
        /* empty action */
        $request->setRequestUri('core/index/index');
        $this->_model->dispatch($request);
        $this->assertEmpty($this->_model->getResponse()->getBody());
        $this->assertEquals($request, $this->_model->getRequest());
    }
}
