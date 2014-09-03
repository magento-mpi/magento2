<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

use Magento\Framework\ObjectManager;

/**
 * CookieMetadataFactory is used to construct SensitiveCookieMetadata and PublicCookieMetadata objects.
 */
class CookieMetadataFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Creates a SensitiveCookieMetadata object with the supplied metadata.
     *
     * @param array $metadata
     * @return SensitiveCookieMetadata
     */
    public function createSensitiveCookieMetadata($metadata = [])
    {
        return $this->objectManager->create(
            'Magento\Framework\Stdlib\Cookie\SensitiveCookieMetadata',
            ['metadata' => $metadata]
        );
    }

    /**
     * Creates a PublicCookieMetadata object with the supplied metadata.
     *
     * @param array $metadata
     * @return PublicCookieMetadata
     */
    public function createPublicCookieMetadata($metadata = [])
    {
        return $this->objectManager->create(
            'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
            ['metadata' => $metadata]
        );
    }

    /**
     * Creates CookieMetadata object with the supplied metadata.
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
