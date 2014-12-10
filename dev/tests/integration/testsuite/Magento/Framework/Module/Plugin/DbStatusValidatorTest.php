<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Plugin;

class DbStatusValidatorTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function testValidationUpToDateDb()
    {
        $this->dispatch('index/index');
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Framework\Module\Exception
     * @expectedExceptionMessage Please update your database
     */
    public function testValidationOutdatedDb()
    {
        $resourceName = 'adminnotification_setup';
        /*reset versions*/
        /** @var \Magento\Framework\Module\ResourceInterface $resource */
        $resource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\Module\ResourceInterface'
        );

        $resource->setDbVersion($resourceName, '0.1');
        $resource->setDataVersion($resourceName, '0.1');

        /** @var \Magento\Framework\Cache\FrontendInterface $cache */
        $cache = $this->_objectManager->get('Magento\Framework\App\Cache\Type\Config');
        $cache->clean();

        /* This triggers plugin to be executed */
        $this->dispatch('index/index');
    }
}
