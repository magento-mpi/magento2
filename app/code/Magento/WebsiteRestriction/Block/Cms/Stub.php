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
 * Stub block that outputs a raw CMS-page
 *
 */
namespace Magento\WebsiteRestriction\Block\Cms;

class Stub extends \Magento\Cms\Block\Page
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Block\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve page from registry if it is not there try to laod it by indetifier
     *
     * @return \Magento\Cms\Model\Page
     */

    public function getPage()
    {
        if (!$this->hasData('page')) {
            $page = $this->_coreRegistry->registry('restriction_landing_page');
            if (!$page) {
                $page = \Mage::getModel('Magento\Cms\Model\Page')
                    ->load($this->getPageIdentifier(), 'identifier');
            }
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    protected function _prepareLayout()
    {
        $page = $this->getPage();

        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('cms-'.$page->getIdentifier());
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());
        }
    }
}
