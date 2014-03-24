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
        $this->assertEmpty($this->_model->dispatch($request)->getBody());
    }
}
