<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Injectable;
use Magento\Store\Test\Fixture\Store;
use Magento\Cms\Test\Page\Adminhtml\CmsBlockNew;
use Magento\Backend\Test\Page\Adminhtml\StoreNew;
use Magento\Cms\Test\Page\Adminhtml\CmsBlockIndex;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;
use Magento\Backend\Test\Page\Adminhtml\StoreDelete;

/**
 * Class CatalogRuleEntityTest
 * Parent class for CMS Block tests
 */
abstract class CmsBlockEntityTest extends Injectable
{
    /**
     * Page CmsBlockIndex
     *
     * @var CmsBlockIndex
     */
    protected $cmsBlockIndex;

    /**
     * Page CmsBlockNew
     *
     * @var CmsBlockNew
     */
    protected $cmsBlockNew;

    /**
     * Page StoreIndex
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Page StoreNew
     *
     * @var StoreNew
     */
    protected $storeNew;

    /**
     * Page StoreDelete
     *
     * @var StoreDelete
     */
    protected $storeDelete;

    /**
     * Store Name
     *
     * @var string
     */
    protected static $storeName;

    /**
     * Injection data
     *
     * @param CmsBlockIndex $cmsBlockIndex
     * @param CmsBlockNew $cmsBlockNew
     * @param StoreIndex $storeIndex
     * @param StoreNew $storeNew
     * @param StoreDelete $storeDelete
     * @return void
     */
    public function __inject(
        CmsBlockIndex $cmsBlockIndex,
        CmsBlockNew $cmsBlockNew,
        StoreIndex $storeIndex,
        StoreNew $storeNew,
        StoreDelete $storeDelete
    ) {
        $this->cmsBlockIndex = $cmsBlockIndex;
        $this->cmsBlockNew = $cmsBlockNew;
        $this->storeIndex = $storeIndex;
        $this->storeNew = $storeNew;
        $this->storeDelete = $storeDelete;
    }

    /**
     * Delete Store after test
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $storeName = reset(self::$storeName);
        $tmp = explode("/", $storeName);
        $filter['store_title'] = end($tmp);
        $storeIndex = Factory::getPageFactory()->getAdminSystemStore();
        $storeIndex->open();
        $storeIndex->getStoreGrid()->searchAndOpen($filter);
        $storeNew = Factory::getPageFactory()->getAdminSystemStoreNewStore();
        $storeNew->getFormPageActions()->delete();
        $storeDelete = Factory::getPageFactory()->getAdminSystemStoreDeleteStore();
        $storeDelete->getStoreForm()->fillForm('No');
        $storeDelete->getFormPageActions()->delete();
    }
}
