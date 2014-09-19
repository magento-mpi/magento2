<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CmsUrlRewrite\Service\V1;

use Magento\Framework\StoreManagerInterface;
use Magento\UrlRedirect\Service\V1\Data\Converter;
use Magento\UrlRedirect\Service\V1\Data\UrlRewrite;

class CmsPageUrlGenerator implements CmsPageUrlGeneratorInterface
{
    /**
     * Entity type code
     */
    const ENTITY_TYPE = 'cms-page';

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $cmsPage;

    /**
     * @param Converter $converter
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Converter $converter,
        StoreManagerInterface $storeManager
    ) {
        $this->converter = $converter;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($cmsPage)
    {
        $stores = $cmsPage->getStores();
        $this->cmsPage = $cmsPage;
        $urls = array_search('0', $stores) === false ? $this->generateForSpecificStores($stores)
            : $this->generateForAllStores();
        $this->cmsPage = null;
        return $urls;
    }

    /**
     * Generate list of urls for default store
     *
     * @return UrlRewrite[]
     */
    protected function generateForAllStores()
    {
        $urls = [];
        foreach ($this->storeManager->getStores() as $store) {
            $urls[] = $this->createUrlRewrite($store->getStoreId());
        }
        return $urls;
    }

    /**
     * Generate list of urls per store
     *
     * @param int[] $storeIds
     * @return UrlRewrite[]
     */
    protected function generateForSpecificStores($storeIds)
    {
        $urls = [];
        $existingStores = $this->storeManager->getStores();
        foreach ($storeIds as $storeId) {
            if (!isset($existingStores[$storeId])) {
                continue;
            }
            $urls[] = $this->createUrlRewrite($storeId);
        }
        return $urls;
    }

    /**
     * Create url rewrite object
     *
     * @param int $storeId
     * @param string|null $redirectType Null or one of OptionProvider const
     * @return UrlRewrite
     */
    protected function createUrlRewrite($storeId, $redirectType = null)
    {
        return $this->converter->convertArrayToObject([
            UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE,
            UrlRewrite::ENTITY_ID => $this->cmsPage->getId(),
            UrlRewrite::STORE_ID => $storeId,
            UrlRewrite::REQUEST_PATH => $this->cmsPage->getIdentifier(),
            UrlRewrite::TARGET_PATH => 'cms/page/view/page_id/' . $this->cmsPage->getId(),
            UrlRewrite::REDIRECT_TYPE => $redirectType,
        ]);
    }
}
