<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib;

class CookieScope
{
    /**
     * @var SensitiveCookieMetadata
     */
    private $sensitiveCookieMetadata;

    /**
     * @var PublicCookieMetadata
     */
    private $publicCookieMetadata;

    /**
     * @param SensitiveCookieMetadata $sensitiveCookieMetadata
     * @param PublicCookieMetadata $publicCookieMetadata
     */
    public function __construct(
        SensitiveCookieMetadata $sensitiveCookieMetadata = null,
        PublicCookieMetadata $publicCookieMetadata = null
    ) {
        $this->sensitiveCookieMetadata = $sensitiveCookieMetadata;
        $this->publicCookieMetadata = $publicCookieMetadata;
    }

    /**
     * Get the default public cookie metadata values for this scope.
     *
     * @return PublicCookieMetadata
     */
    public function getPublicCookieMetadata()
    {
        return $this->publicCookieMetadata;
    }

    /**
     * Get the default secure cookie metadata values for this scope.
     *
     * @return SensitiveCookieMetadata
     */
    public function getSensitiveCookieMetadata()
    {
        return $this->sensitiveCookieMetadata;
    }

}
