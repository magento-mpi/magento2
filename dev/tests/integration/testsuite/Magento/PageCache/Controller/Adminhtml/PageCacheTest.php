<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Page_Cache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Controller\Adminhtml;

use Magento\TestFramework\Bootstrap;

/**
 * @magentoAppArea adminhtml
 * @SuppressWarnings
 */
class PageCacheTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @codingStandardsIgnoreStart
     * @magentoConfigFixture current_store system/varnish_configuration_settings/ttl 360
     * @magentoConfigFixture current_store system/varnish_configuration_settings/access_list 127.0.0.1
     * @magentoConfigFixture current_store system/varnish_configuration_settings/backend_port 8080
     * @magentoConfigFixture current_store system/varnish_configuration_settings/backend_host 127.0.0.1
     * @magentoConfigFixture current_store design/theme/ua_regexp a:1:{i:0;a:2:{s:6:"regexp";s:4:"/ie/";s:5:"value";i:1;}}
     * @codingStandardsIgnoreEnd
     */
    public function testExportVarnishConfigAction()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\UrlInterface')
            ->turnOffSecretKey();

        // \Magento\Backend\Model\Auth\StorageInterface
        $auth = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Auth');
        $auth->login(Bootstrap::ADMIN_NAME, Bootstrap::ADMIN_PASSWORD);
        $auth->getAuthStorage()->setIsFirstPageAfterLogin(false);

        $configModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\ConfigInterface');
        $data = 'Magento/PageCache/etc/varnish.vcl';
        $configModel->setValue('system/page_cache/varnish_configuration_settings_path', $data);

        $this->dispatch('backend/admin/PageCache/exportVarnishConfig');
        $body = $this->getResponse()->getBody();
        /** @var \Magento\App\Filesystem $filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Filesystem');
        $rootDir = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $path = $rootDir->getRelativePath(realpath(dirname(dirname(__DIR__))) . '/_files/varnish.vcl');
        $fileContent = $rootDir->readFile($path);
        $this->assertEquals($fileContent, $body);
    }
}
