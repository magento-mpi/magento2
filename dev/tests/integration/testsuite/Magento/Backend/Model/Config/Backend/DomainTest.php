<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend;
use Magento\Framework\Model\Exception;

/**
 * Test \Magento\Backend\Model\Config\Backend\Domain
 *
 * @magentoAppArea adminhtml
 */
class DomainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $value
     * @param string $exceptionMessage
     * @magentoDbIsolation enabled
     * @dataProvider beforeSaveDataProvider
     */
    public function testBeforeSave($value, $exceptionMessage = null)
    {
        /** @var $domain \Magento\Backend\Model\Config\Backend\Domain */
        $domain = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Backend\Model\Config\Backend\Domain'
        );
        $domain->setValue($value);
        try {
            $domain->save();
            if ($exceptionMessage ) {
                $this->fail('Failed to throw exception');
            } else {
                $this->assertNotNull($domain->getId());
            }
        } catch (Exception $e) {
            $this->assertContains('Invalid domain name: ', $e->getMessage());
            $this->assertContains($exceptionMessage, $e->getMessage());
            $this->assertNull($domain->getId());
        }
    }

    /**
     * @return array
     */
    public function beforeSaveDataProvider()
    {
        return [
            'not string' => [['array'], 'Invalid type given. String expected'],
            'invalid hostname' => [
                'http://',
                'The input does not match the expected structure for a DNS hostname; '
                . 'The input does not appear to be a valid URI hostname; '
                . 'The input does not appear to be a valid local network name'
            ],
            'valid hostname' => ['hostname.com'],
        ];
    }
}
