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
namespace Magento\Cms\Block;

class Page extends \Magento\Core\Block\AbstractBlock
{
    /**
     * Cms data
     *
     * @var \Magento\Cms\Helper\Data
     */
    protected $_cmsData = null;

    /**
     * @param \Magento\Cms\Helper\Data $cmsData
     * @param \Magento\Core\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Helper\Data $cmsData,
        \Magento\Core\Block\Context $context,
        array $data = array()
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Page instance
     *
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        if (!$this->hasData('page')) {
            if ($this->getPageId()) {
                $page = \Mage::getModel('Magento\Cms\Model\Page')
                    ->setStoreId(\Mage::app()->getStore()->getId())
                    ->load($this->getPageId(), 'identifier');
            } else {
                $page = \Mage::getSingleton('Magento\Cms\Model\Page');
            }
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    /**
     * Prepare global layout
     *
     * @return \Magento\Cms\Block\Page
     */
    protected function _prepareLayout()
    {
        $page = $this->getPage();

        // show breadcrumbs
        if (\Mage::getStoreConfig('web/default/show_cms_breadcrumbs')
            && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))
            && ($page->getIdentifier()!==\Mage::getStoreConfig('web/default/cms_home_page'))
            && ($page->getIdentifier()!==\Mage::getStoreConfig('web/default/cms_no_route'))) {
                $breadcrumbs->addCrumb('home', array('label'=>__('Home'), 'title'=>__('Go to Home Page'), 'link'=>\Mage::getBaseUrl()));
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
        /* @var $helper \Magento\Cms\Helper\Data */
        $helper = $this->_cmsData;
        $processor = $helper->getPageTemplateProcessor();
        $html = $processor->filter($this->getPage()->getContent());
        $html = $this->getLayout()->renderElement('messages') . $html;
        return $html;
    }
}
