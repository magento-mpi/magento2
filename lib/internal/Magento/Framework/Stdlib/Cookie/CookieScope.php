<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

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
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;


    /**
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SensitiveCookieMetadata $sensitiveCookieMetadata
     * @param PublicCookieMetadata $publicCookieMetadata
     */
    public function __construct(
        CookieMetadataFactory $cookieMetadataFactory,
        SensitiveCookieMetadata $sensitiveCookieMetadata = null,
        PublicCookieMetadata $publicCookieMetadata = null
    ) {
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sensitiveCookieMetadata = $sensitiveCookieMetadata;
        $this->publicCookieMetadata = $publicCookieMetadata;
    }

    /**
     * Merges the input override metadata with any defaults set on this Scope, and then returns a CookieMetadata
     * object representing the merged values.
     *
     * @param SensitiveCookieMetadata|null $override
     * @return SensitiveCookieMetadata
     */
    public function getSensitiveCookieMetadata(SensitiveCookieMetadata $override = null)
    {
        if (!is_null($this->sensitiveCookieMetadata)) {
            $merged = $this->sensitiveCookieMetadata->__toArray();
        } else {
            $merged = [];
        }
        if (!is_null($override)) {
            $merged = array_merge($merged, $override->__toArray());
        }

        return $this->cookieMetadataFactory->createSensitiveCookieMetadata($merged);
    }

    /**
     * Merges the input override metadata with any defaults set on this Scope, and then returns a CookieMetadata
     * object representing the merged values.
     *
     * @param PublicCookieMetadata|null $override
     * @return PublicCookieMetadata
     */
    public function getPublicCookieMetadata(PublicCookieMetadata $override = null)
    {
        if (!is_null($this->publicCookieMetadata)) {
            $merged = $this->publicCookieMetadata->__toArray();
        } else {
            $merged = [];
        }
        if (!is_null($override)) {
            $merged = array_merge($merged, $override->__toArray());
        }

        return $this->cookieMetadataFactory->createPublicCookieMetadata($merged);
    }

}
