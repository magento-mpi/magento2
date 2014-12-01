<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Tools\SampleData\Helper;

/**
 * Class StoreManager
 */
class StoreManager
{
    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \Magento\Store\Model\Group
     */
    protected $group;

    /**
     * @var \Magento\Store\Model\Website
     */
    protected $website;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @return bool
     */
    protected function loadStore()
    {
        $isLoaded = true;
        if (!$this->website) {
            $isLoaded = false;
            $websites = $this->storeManager->getWebsites();
            foreach ($websites as $website) {
                if ($website->getIsDefault()) {
                    $this->website = $website;
                    $this->group = $website->getDefaultGroup();
                    $this->store = $website->getDefaultStore();
                    $isLoaded = true;
                    break;
                }
            }
        }
        return $isLoaded;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        $this->loadStore();
        return $this->store->getId();
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        $this->loadStore();
        return $this->website->getId();
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        $this->loadStore();
        return $this->group->getId();
    }
}
