<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model;
use \Magento\TestFramework\Helper\Bootstrap;

class UrlRewriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\UrlRewrite\Model\UrlRewrite
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->model = $this->objectManager->create(
            'Magento\UrlRewrite\Model\UrlRewrite'
        );
    }

    public function testLoadByRequestPath()
    {
        $this->model->setStoreId(
            $this->objectManager->get(
                'Magento\Store\Model\StoreManagerInterface'
            )->getDefaultStoreView()->getId()
        )->setRequestPath(
            'fancy/url.html'
        )->setTargetPath(
            'catalog/product/view'
        )->setIsSystem(
            1
        )->setOptions(
            'RP'
        )->save();

        try {
            $read = $this->objectManager->create(
                'Magento\UrlRewrite\Model\UrlRewrite'
            );
            $read->setStoreId(
                $this->objectManager->get(
                    'Magento\Store\Model\StoreManagerInterface'
                )->getDefaultStoreView()->getId()
            )->loadByRequestPath(
                'fancy/url.html'
            );

            $this->assertEquals($this->model->getStoreId(), $read->getStoreId());
            $this->assertEquals($this->model->getRequestPath(), $read->getRequestPath());
            $this->assertEquals($this->model->getTargetPath(), $read->getTargetPath());
            $this->assertEquals($this->model->getIsSystem(), $read->getIsSystem());
            $this->assertEquals($this->model->getOptions(), $read->getOptions());
            $this->model->delete();
        } catch (\Exception $e) {
            $this->model->delete();
            throw $e;
        }
    }

    public function testLoadByIdPath()
    {
        $this->model->setStoreId(
            $this->objectManager->get(
                'Magento\Store\Model\StoreManagerInterface'
            )->getDefaultStoreView()->getId()
        )->setRequestPath(
            'product1.html'
        )->setTargetPath(
            'catalog/product/view/id/1'
        )->setIdPath(
            'product/1'
        )->setIsSystem(
            1
        )->setOptions(
            'RP'
        )->save();

        try {
            $read = $this->objectManager->create(
                'Magento\UrlRewrite\Model\UrlRewrite'
            );
            $read->setStoreId(
                $this->objectManager->get(
                    'Magento\Store\Model\StoreManagerInterface'
                )->getDefaultStoreView()->getId()
            )->loadByIdPath(
                'product/1'
            );
            $this->assertEquals($this->model->getStoreId(), $read->getStoreId());
            $this->assertEquals($this->model->getRequestPath(), $read->getRequestPath());
            $this->assertEquals($this->model->getTargetPath(), $read->getTargetPath());
            $this->assertEquals($this->model->getIdPath(), $read->getIdPath());
            $this->assertEquals($this->model->getIsSystem(), $read->getIsSystem());
            $this->assertEquals($this->model->getOptions(), $read->getOptions());
            $this->model->delete();
        } catch (\Exception $e) {
            $this->model->delete();
            throw $e;
        }
    }

    public function testHasOption()
    {
        $this->model->setOptions('RP');
        $this->assertTrue($this->model->hasOption('RP'));
    }

    public function testRewrite()
    {
        $request = $this->objectManager->create(
            'Magento\Framework\App\RequestInterface'
        )->setPathInfo(
            'fancy/url.html'
        );
        $_SERVER['QUERY_STRING'] = 'foo=bar&___fooo=bar';

        $this->model->setRequestPath(
            'fancy/url.html'
        )->setTargetPath(
            'another/fancy/url.html'
        )->setIsSystem(
            1
        )->save();

        try {
            $this->assertTrue($this->model->rewrite($request));
            $this->assertEquals('/another/fancy/url.html?foo=bar', $request->getRequestUri());
            $this->assertEquals('another/fancy/url.html', $request->getPathInfo());
            $this->model->delete();
        } catch (\Exception $e) {
            $this->model->delete();
            throw $e;
        }
    }

    public function testRewriteSetCookie()
    {
        $request = $this->objectManager->create(
            'Magento\Framework\App\RequestInterface'
        )->setPathInfo(
            'fancy/url.html'
        );

        $_SERVER['QUERY_STRING'] = 'foo=bar';

        $context = $this->objectManager->create(
            '\Magento\Framework\Model\Context'
        );
        $registry = $this->objectManager->create(
            '\Magento\Framework\Registry'
        );
        $scopeConfig = $this->objectManager->create(
            '\Magento\Framework\App\Config\ScopeConfigInterface'
        );
        $cookieMetadataFactory = $this->objectManager->create(
            '\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory'
        );
        $cookieManager = $this->objectManager->create(
            '\Magento\Framework\Stdlib\CookieManager'
        );
        $storeManager = $this->objectManager->create(
            '\Magento\Store\Model\StoreManagerInterface'
        );
        $httpContext = $this->objectManager->create(
            '\Magento\Framework\App\Http\Context'
        );

        $constructorArgs = [
            'context' => $context,
            'registry' => $registry,
            'scopeConfig' => $scopeConfig,
            'cookieMetadataFactory' => $cookieMetadataFactory,
            'cookieManager' => $cookieManager,
            'storeManager' => $storeManager,
            'httpContext' => $httpContext,
        ];

        //SUT must be mocked out for this test to prevent headers from being sent,
        //causing errors.
        $modelMock = $this->getMock('\Magento\UrlRewrite\Model\UrlRewrite',
            ['_sendRedirectHeaders'],
            $constructorArgs
        );

        $modelMock->setRequestPath(
            'fancy/url.html'
        )->setTargetPath(
            'http:/url.html'
        )->save();

        try {
            $this->assertTrue($modelMock->rewrite($request));
            $this->assertEquals($_COOKIE[\Magento\Store\Model\Store::COOKIE_NAME], 'admin');
            $modelMock->delete();
        } catch (\Exception $e) {
            $modelMock->delete();
            throw $e;
        }
    }

    public function testRewriteNonExistingRecord()
    {
        $request = $this->objectManager
            ->create('Magento\Framework\App\RequestInterface');
        $this->assertFalse($this->model->rewrite($request));
    }

    public function testRewriteWrongStore()
    {
        $request = $this->objectManager
            ->create('Magento\Framework\App\RequestInterface');
        $_GET['___from_store'] = uniqid('store');
        $this->assertFalse($this->model->rewrite($request));
    }

    public function testRewriteNonExistingRecordCorrectStore()
    {
        $request = $this->objectManager
            ->create('Magento\Framework\App\RequestInterface');
        $_GET['___from_store'] = $this->objectManager->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getDefaultStoreView()->getCode();
        $this->assertFalse($this->model->rewrite($request));
    }

    public function testGetStoreId()
    {
        $this->model->setStoreId(10);
        $this->assertEquals(10, $this->model->getStoreId());
    }

    public function testCRUD()
    {
        $this->model->setStoreId(
            $this->objectManager->get(
                'Magento\Store\Model\StoreManagerInterface'
            )->getDefaultStoreView()->getId()
        )->setRequestPath(
            'fancy/url.html'
        )->setTargetPath(
            'catalog/product/view'
        )->setIsSystem(
            1
        )->setOptions(
            'RP'
        );
        $crud = new \Magento\TestFramework\Entity($this->model, ['request_path' => 'fancy/url2.html']);
        $crud->testCrud();
    }
}
