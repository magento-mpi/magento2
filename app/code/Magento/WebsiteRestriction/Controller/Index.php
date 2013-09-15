<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website stub controller
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\WebsiteRestriction\Controller;

class Index extends \Magento\Core\Controller\Front\Action
{
    protected $_stubPageIdentifier = \Magento\WebsiteRestriction\Helper\Data::XML_PATH_RESTRICTION_LANDING_PAGE;

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    protected $_cacheKey;

    /**
     * Prefix for cache id
     */
    protected $_cacheKeyPrefix = 'RESTRICTION_LANGING_PAGE_';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        \Magento\Core\Model\Cache\Type\Config $configCacheType
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->_configCacheType = $configCacheType;
    }

    protected function _construct()
    {
        $this->_cacheKey = $this->_cacheKeyPrefix . \Mage::app()->getWebsite()->getId();
    }

    /**
     * Display a pre-cached CMS-page if we have such or generate new one
     *
     */
    public function stubAction()
    {
        $cachedData = $this->_configCacheType->load($this->_cacheKey);
        if ($cachedData) {
            $this->getResponse()->setBody($cachedData);
        } else {
            /**
             * Generating page and save it to cache
             */
            $page = \Mage::getModel('Magento\Cms\Model\Page')
                ->load(\Mage::getStoreConfig($this->_stubPageIdentifier), 'identifier');

            $this->_coreRegistry->register('restriction_landing_page', $page);

            if ($page->getCustomTheme()) {
                if (\Mage::app()->getLocale()
                    ->isStoreDateInInterval(null, $page->getCustomThemeFrom(), $page->getCustomThemeTo())
                ) {
                    $this->_objectManager->get('Magento\Core\Model\View\DesignInterface')
                        ->setDesignTheme($page->getCustomTheme());
                }
            }

            $this->addActionLayoutHandles();

            if ($page->getRootTemplate()) {
                $this->_objectManager->get('Magento\Page\Helper\Layout')
                    ->applyHandle($page->getRootTemplate());
            }

            $this->loadLayoutUpdates();

            $this->getLayout()->getUpdate()->addUpdate($page->getLayoutUpdateXml());
            $this->generateLayoutXml()->generateLayoutBlocks();

            if ($page->getRootTemplate()) {
                $this->_objectManager->get('Magento\Page\Helper\Layout')
                    ->applyTemplate($page->getRootTemplate());
            }

            $this->renderLayout();

            $this->_configCacheType->save(
                $this->getResponse()->getBody(), $this->_cacheKey, array(\Magento\Core\Model\Website::CACHE_TAG)
            );
        }
    }
}
