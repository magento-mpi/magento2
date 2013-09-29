<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ImportExport\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class ImportTest extends \Magento\Backend\Utility\Controller
{
    public function testGetFilterAction()
    {
        $this->dispatch('backend/admin/import/index');
        $body = $this->getResponse()->getBody();
        $this->assertContains(
            (string)\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\ImportExport\Helper\Data')
                ->getMaxUploadSizeMessage(),
            $body
        );
    }
}
