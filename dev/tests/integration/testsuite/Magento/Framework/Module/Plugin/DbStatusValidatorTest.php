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

    public function testValidationOutdatedDb()
    {
        $resourceName = 'adminnotification_setup';
        /*reset versions*/
        /** @var \Magento\Framework\Module\ResourceInterface $resource */
        $resource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\Module\ResourceInterface'
        );
        $dbVersion = $resource->getDbVersion($resourceName);
        $dbDataVersion = $resource->getDataVersion($resourceName);
        try {
            $resource->setDbVersion($resourceName, '0.1');
            $resource->setDataVersion($resourceName, '0.1');
            /** @var \Magento\Framework\Cache\FrontendInterface $cache */
            $cache = $this->_objectManager->get('Magento\Framework\App\Cache\Type\Config');
            $cache->clean();

            try {
                /* This triggers plugin to be executed */
                $this->dispatch('index/index');
            } catch (\Magento\Framework\Module\Exception $e) {
                if ($e->getMessage() != 'Please update your database: first run "composer install" from the Magento ' .
                    'root/ and root/setup directories. Then run "php –f index.php update" from the Magento ' .
                    'root/setup directory.' . PHP_EOL .
                    'Error details: database is out of date.' . PHP_EOL .
                    'Magento_AdminNotification schema: current version - 0.1, latest version - 2.0.0.0' . PHP_EOL .
                    'Magento_AdminNotification data: current version - 0.1, latest version - 2.0.0.0'
                ) {
                    $failureMessage = "DB status validation doesn't work properly. Caught exception message is '"
                        . $e->getMessage() ."'";
                }
            }
        } catch (\Exception $e) {
            $failureMessage = "Impossible to continue other tests, because database is broken: {$e}";
        }

        $resource->setDbVersion($resourceName, $dbVersion);
        $resource->setDataVersion($resourceName, $dbDataVersion);

        if (isset($failureMessage)) {
            $this->fail($failureMessage);
        }
    }
}
