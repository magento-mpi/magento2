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

namespace Magento\Core\Controller\Varien;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FrontTest extends \PHPUnit_Framework_TestCase
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

    public function testSetGetDefault()
    {
        $this->_model->setDefault('test', 'value');
        $this->assertEquals('value', $this->_model->getDefault('test'));

        $default = array('some_key' => 'some_value');
        $this->_model->setDefault($default);
        $this->assertEquals($default, $this->_model->getDefault());
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
        /* empty action */
        $this->_model->getRequest()->setRequestUri('core/index/index');
        $this->_model->dispatch($request);
        $this->assertEmpty($this->_model->getResponse()->getBody());
    }

    /**
     * Data provider for testApplyRewrites
     *
     * @return array
     */
    public function applyRewritesDataProvider()
    {
        return array(
            'url rewrite' => array(
                '$sourcePath' => '/test_rewrite_path',      // data from fixture
                '$resultPath' => 'cms/page/view/page_id/1', // data from fixture
            ),
            'configuration rewrite' => array(
                '$sourcePath' => '/test/url/',
                '$resultPath' => '/new_test/url/subdirectory/',
            ),
        );
    }
}
