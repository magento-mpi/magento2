<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

use Magento\Framework\ObjectManager;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test CookieScope
 *
 */
class CookieScopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();

    }

    public function testGetSensitiveCookieMetadataEmpty()
    {
        $cookieScope = $this->createCookieScope();

        $this->assertEquals([], $cookieScope->getSensitiveCookieMetadata()->__toArray());
    }

    public function testGetPublicCookieMetadataEmpty()
    {
        $cookieScope = $this->createCookieScope();

        $this->assertEquals([], $cookieScope->getPublicCookieMetadata()->__toArray());
    }

    public function testGetSensitiveCookieMetadataDefaults()
    {
        $defaultValues = [
            SensitiveCookieMetadata::KEY_PATH => 'default path',
            SensitiveCookieMetadata::KEY_DOMAIN => 'default domain',
        ];
        $sensitive = $this->createSensitiveMetadata($defaultValues);
        $cookieScope = $this->createCookieScope(
            [
                'sensitiveCookieMetadata' => $sensitive,
                'publicCookieMetadata' => null,
                'cookieMetadata' => null
            ]
        );

        $this->assertEquals([], $cookieScope->getPublicCookieMetadata()->__toArray());
        $this->assertEquals([], $cookieScope->getCookieMetadata()->__toArray());
        $this->assertEquals($defaultValues, $cookieScope->getSensitiveCookieMetadata()->__toArray());
    }

    public function testGetPublicCookieMetadataDefaults()
    {
        $defaultValues = [
            PublicCookieMetadata::KEY_PATH => 'default path',
            PublicCookieMetadata::KEY_DOMAIN => 'default domain',
            PublicCookieMetadata::KEY_DURATION => 'default duration',
            PublicCookieMetadata::KEY_HTTP_ONLY => 'default http',
            PublicCookieMetadata::KEY_SECURE => 'default secure',
        ];
        $public = $this->createPublicMetadata($defaultValues);
        $cookieScope = $this->createCookieScope(
            [
                'sensitiveCookieMetadata' => null,
                'publicCookieMetadata' => $public,
                'cookieMetadata' => null
            ]
        );

        $this->assertEquals([], $cookieScope->getSensitiveCookieMetadata()->__toArray());
        $this->assertEquals([], $cookieScope->getCookieMetadata()->__toArray());
        $this->assertEquals($defaultValues, $cookieScope->getPublicCookieMetadata()->__toArray());
    }

    public function testGetCookieMetadataDefaults()
    {
        $defaultValues = [
            CookieMetadata::KEY_PATH => 'default path',
            CookieMetadata::KEY_DOMAIN => 'default domain',
        ];
        $cookieMetadata = $this->createCookieMetadata($defaultValues);
        $cookieScope = $this->createCookieScope(
            [
                'sensitiveCookieMetadata' => null,
                'publicCookieMetadata' => null,
                'cookieMetadata' => $cookieMetadata
            ]
        );

        $this->assertEquals([], $cookieScope->getSensitiveCookieMetadata()->__toArray());
        $this->assertEquals([], $cookieScope->getPublicCookieMetadata()->__toArray());
        $this->assertEquals($defaultValues, $cookieScope->getCookieMetadata()->__toArray());
    }

    public function testGetSensitiveCookieMetadataOverrides()
    {
        $defaultValues = [
            SensitiveCookieMetadata::KEY_PATH => 'default path',
            SensitiveCookieMetadata::KEY_DOMAIN => 'default domain',
        ];
        $overrideValues = [
            SensitiveCookieMetadata::KEY_PATH => 'override path',
            SensitiveCookieMetadata::KEY_DOMAIN => 'override domain',
        ];
        $sensitive = $this->createSensitiveMetadata($defaultValues);
        $cookieScope = $this->createCookieScope(
            [
                'sensitiveCookieMetadata' => $sensitive,
                'publicCookieMetadata' => null,
                'cookieMetadata' => null
            ]
        );
        $override = $this->createSensitiveMetadata($overrideValues);

        $this->assertEquals([], $cookieScope->getPublicCookieMetadata($this->createPublicMetadata())->__toArray());
        $this->assertEquals([], $cookieScope->getCookieMetadata($this->createCookieMetadata())->__toArray());
        $this->assertEquals($overrideValues, $cookieScope->getSensitiveCookieMetadata($override)->__toArray());
    }

    public function testGetPublicCookieMetadataOverrides()
    {
        $defaultValues = [
            PublicCookieMetadata::KEY_PATH => 'default path',
            PublicCookieMetadata::KEY_DOMAIN => 'default domain',
            PublicCookieMetadata::KEY_DURATION => 'default duration',
            PublicCookieMetadata::KEY_HTTP_ONLY => 'default http',
            PublicCookieMetadata::KEY_SECURE => 'default secure',
        ];
        $overrideValues = [
            PublicCookieMetadata::KEY_PATH => 'override path',
            PublicCookieMetadata::KEY_DOMAIN => 'override domain',
            PublicCookieMetadata::KEY_DURATION => 'override duration',
            PublicCookieMetadata::KEY_HTTP_ONLY => 'override http',
            PublicCookieMetadata::KEY_SECURE => 'override secure',
        ];
        $public = $this->createPublicMetadata($defaultValues);
        $cookieScope = $this->createCookieScope(
            [
                'sensitiveCookieMetadata' => null,
                'publicCookieMetadata' => $public,
                'cookieMetadata' => null
            ]
        );
        $override = $this->createPublicMetadata($overrideValues);

        $this->assertEquals(
            [],
            $cookieScope->getSensitiveCookieMetadata($this->createSensitiveMetadata())->__toArray()
        );
        $this->assertEquals([], $cookieScope->getCookieMetadata($this->createCookieMetadata())->__toArray());
        $this->assertEquals($overrideValues, $cookieScope->getPublicCookieMetadata($override)->__toArray());
    }

    public function testGetCookieMetadataOverrides()
    {
        $defaultValues = [
            CookieMetadata::KEY_PATH => 'default path',
            CookieMetadata::KEY_DOMAIN => 'default domain',
        ];
        $overrideValues = [
            CookieMetadata::KEY_PATH => 'override path',
            CookieMetadata::KEY_DOMAIN => 'override domain',
        ];
        $cookieMetadata = $this->createCookieMetadata($defaultValues);
        $cookieScope = $this->createCookieScope(
            [
                'sensitiveCookieMetadata' => null,
                'publicCookieMetadata' => null,
                'cookieMetadata' => $cookieMetadata
            ]
        );
        $override = $this->createCookieMetadata($overrideValues);

        $this->assertEquals(
            [],
            $cookieScope->getSensitiveCookieMetadata($this->createSensitiveMetadata())->__toArray()
        );
        $this->assertEquals(
            [],
            $cookieScope->getPublicCookieMetadata($this->createPublicMetadata())->__toArray()
        );
        $this->assertEquals($overrideValues, $cookieScope->getCookieMetadata($override)->__toArray());
    }

    /**
     * Creates a CookieScope object with the given parameters.
     *
     * @param array $params
     * @return CookieScope
     */
    private function createCookieScope($params = [])
    {
        return $this->objectManager->create('Magento\Framework\Stdlib\Cookie\CookieScope', $params);
    }

    /**
     * Creates a SensitiveCookieMetadata object with provided metadata values.
     *
     * @param array $metadata
     * @return SensitiveCookieMetadata
     */
    public function createSensitiveMetadata($metadata = [])
    {
        return $this->objectManager->create(
            'Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata',
            ['metadata' => $metadata]
        );
    }

    /**
     * Creates a PublicCookieMetadata object with provided metadata values.
     *
     * @param array $metadata
     * @return PublicCookieMetadata
     */
    public function createPublicMetadata($metadata = [])
    {
        return $this->objectManager->create(
            'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
            ['metadata' => $metadata]
        );
    }

    /**
     * Creates a CookieMetadata object with provided metadata values.
     *
     * @param array $metadata
     * @return CookieMetadata
     */
    public function createCookieMetadata($metadata = [])
    {
        return $this->objectManager->create(
            'Magento\Framework\Stdlib\Cookie\CookieMetadata',
            ['metadata' => $metadata]
        );
    }

}
