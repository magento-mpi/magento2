<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms page content block
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Block_Page extends Magento_Core_Block_Abstract
{
    /**
     * @var Magento_Cms_Model_Template_FilterProvider
     */
    protected $_filterProvider;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Block_Context $context
     * @param Magento_Cms_Model_Template_FilterProvider $filterProvider
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Context $context,
        Magento_Cms_Model_Template_FilterProvider $filterProvider,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Cms_Model_PageFactory $pageFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_pageFactory = $pageFactory;
    }

    /**
     * Retrieve Page instance
     *
     * @return Magento_Cms_Model_Page
     */
    public function getPage()
    {
        if (!$this->hasData('page')) {
            /** @var Magento_Cms_Model_Page $page */
            $page = $this->_pageFactory->create();
            if ($this->getPageId()) {
                $page->setStoreId($this->_storeManager->getStore()->getId())
                    ->load($this->getPageId(), 'identifier');
            }
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    /**
     * Prepare global layout
     *
     * @return Magento_Cms_Block_Page
     */
    protected function _prepareLayout()
    {
        $page = $this->getPage();

        // show breadcrumbs
        if ($this->_storeConfig->getConfig('web/default/show_cms_breadcrumbs')
            && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))
            && ($page->getIdentifier()!==$this->_storeConfig->getConfig('web/default/cms_home_page'))
            && ($page->getIdentifier()!==$this->_storeConfig->getConfig('web/default/cms_no_route'))) {
                $breadcrumbs->addCrumb('home', array('label'=>__('Home'), 'title'=>__('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()));
                $breadcrumbs->addCrumb('cms_page', array('label'=>$page->getTitle(), 'title'=>$page->getTitle()));
        }

        $root = $this->getLayout()->getBlock('root');
        if ($root) {
            $root->addBodyClass('cms-'.$page->getIdentifier());
        }

        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());
        }

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            // Setting empty page title if content heading is absent
            $cmsTitle = $page->getContentHeading() ? : ' ';
            $pageMainTitle->setPageTitle($this->escapeHtml($cmsTitle));
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare HTML content
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = $this->_filterProvider->getPageFilter()->filter($this->getPage()->getContent());
        $html = $this->getLayout()->renderElement('messages') . $html;
        return $html;
    }
}
