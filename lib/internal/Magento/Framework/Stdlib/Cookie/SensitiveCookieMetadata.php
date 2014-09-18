<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

use Magento\Framework\StoreManagerInterface;

/**
 * Class SensitiveCookieMetadata
 *
 * The class has only methods extended from CookieMetadata
 * as path and domain are only data to be exposed by SensitiveCookieMetadata
 */
class SensitiveCookieMetadata extends CookieMetadata
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param array $metadata
     */
    public function __construct(StoreManagerInterface $storeManager, $metadata = [])
    {
        if (!isset($metadata[self::KEY_HTTP_ONLY])) {
            $metadata[self::KEY_HTTP_ONLY] = true;
        }
        $this->storeManager = $storeManager;
        parent::__construct($metadata);
    }


    /**
     * {@inheritdoc}
     */
    public function getSecure()
    {
        $this->updateSecureValue();
        return $this->get(self::KEY_SECURE);
    }

    /**
     * {@inheritdoc}
     */
    public function __toArray()
    {
        $this->updateSecureValue();
        return parent::__toArray();
    }

    /**
     * Update secure value, set it to store setting if it has no explicit value assigned.
     *
     * @return void
     */
    private function updateSecureValue()
    {
        if (null === $this->get(self::KEY_SECURE)) {
            $store = $this->storeManager->getStore();
            if (empty($store)) {
                $this->set(self::KEY_SECURE, true);
            } else {
                $this->set(self::KEY_SECURE, $store->isCurrentlySecure());
            }
        }
    }
}
