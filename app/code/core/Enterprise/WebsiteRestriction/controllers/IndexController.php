<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website stub controller
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_WebsiteRestriction_IndexController extends Mage_Core_Controller_Front_Action
{
    protected $_stubPageIdentifier = Enterprise_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_LANDING_PAGE;

    protected $_cacheKey;

    /**
     * Prefix for cache id
     */
    protected $_cacheKeyPrefix = 'RESTRICTION_LANGING_PAGE_';

    /**
     * Cache  will be ralted on configuration and website
     *
     * @var array
     */
    protected $_cacheTags = array(
        Mage_Core_Model_Website::CACHE_TAG,
        Mage_Core_Model_Config::CACHE_TAG
    );

    protected function _construct()
    {
        $this->_cacheKey = $this->_cacheKeyPrefix . Mage::app()->getWebsite()->getId();
    }

    /**
     * Display a pre-cached CMS-page if we have such or generate new one
     *
     */
    public function stubAction()
    {
        $cachedData = Mage::app()->loadCache($this->_cacheKey);
        if ($cachedData) {
            $this->getResponse()->setBody($cachedData);
        } else {
            /**
             * Generating page and save it to cache
             */
            $page = Mage::getModel('Mage_Cms_Model_Page')
                ->load(Mage::getStoreConfig($this->_stubPageIdentifier), 'identifier');

            Mage::register('restriction_landing_page', $page);

            if ($page->getCustomTheme()) {
                if (Mage::app()->getLocale()
                    ->isStoreDateInInterval(null, $page->getCustomThemeFrom(), $page->getCustomThemeTo())
                ) {
                    Mage::getDesign()->setDesignTheme($page->getCustomTheme());
                }
            }

            $this->addActionLayoutHandles();

            if ($page->getRootTemplate()) {
                $this->getLayout()->helper('Mage_Page_Helper_Layout')
                    ->applyHandle($page->getRootTemplate());
            }

            $this->loadLayoutUpdates();

            $this->getLayout()->getUpdate()->addUpdate($page->getLayoutUpdateXml());
            $this->generateLayoutXml()->generateLayoutBlocks();

            if ($page->getRootTemplate()) {
                $this->getLayout()->helper('Mage_Page_Helper_Layout')
                    ->applyTemplate($page->getRootTemplate());
            }

            $this->renderLayout();

            Mage::app()->saveCache($this->getResponse()->getBody(), $this->_cacheKey, $this->_cacheTags);
        }
    }
}
