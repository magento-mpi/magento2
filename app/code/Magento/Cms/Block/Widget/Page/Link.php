<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Block\Widget\Page;

/**
 * Widget to display link to CMS page
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Link
    extends \Magento\View\Element\Html\Link
    implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Prepared href attribute
     *
     * @var string
     */
    protected $_href;

    /**
     * Prepared title attribute
     *
     * @var string
     */
    protected $_title;

    /**
     * Prepared anchor text
     *
     * @var string
     */
    protected $_anchorText;

    /**
     * @var \Magento\Cms\Model\Resource\Page
     */
    protected $_resourcePage;

    /**
     * Cms page
     *
     * @var \Magento\Cms\Helper\Page
     */
    protected $_cmsPage;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Cms\Model\Resource\Page $resourcePage
     * @param \Magento\Cms\Helper\Page $cmsPage
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Cms\Model\Resource\Page $resourcePage,
        \Magento\Cms\Helper\Page $cmsPage,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_resourcePage = $resourcePage;
        $this->_cmsPage = $cmsPage;
    }

    /**
     * Prepare page url. Use passed identifier
     * or retrieve such using passed page id.
     *
     * @return string
     */
    public function getHref()
    {
        if (!$this->_href) {
            $this->_href = '';
            if ($this->getData('href')) {
                $this->_href = $this->getData('href');
            } else if ($this->getData('page_id')) {
                $this->_href = $this->_cmsPage->getPageUrl($this->getData('page_id'));
            }
        }

        return $this->_href;
    }

    /**
     * Prepare anchor title attribute using passed title
     * as parameter or retrieve page title from DB using passed identifier or page id.
     *
     * @return string
     */
    public function getTitle()
    {
        if (!$this->_title) {
            $this->_title = '';
            if ($this->getData('title') !== null) {
                // compare to null used here bc user can specify blank title
                $this->_title = $this->getData('title');
            } else if ($this->getData('page_id')) {
                $this->_title = $this->_resourcePage->getCmsPageTitleById($this->getData('page_id'));
            } else if ($this->getData('href')) {
                $this->_title = $this->_resourcePage->setStore($this->_storeManager->getStore())
                    ->getCmsPageTitleByIdentifier($this->getData('href'));
            }
        }

        return $this->_title;
    }

    /**
     * Prepare label using passed text as parameter.
     * If anchor text was not specified use title instead and
     * if title will be blank string, page identifier will be used.
     *
     * @return string
     */
    public function getLabel()
    {
        if ($this->getData('anchor_text')) {
            $this->_anchorText = $this->getData('anchor_text');
        } else if ($this->getTitle()) {
            $this->_anchorText = $this->getTitle();
        } else if ($this->getData('href')) {
            $this->_anchorText = $this->_resourcePage->setStore($this->_storeManager->getStore())
                ->getCmsPageTitleByIdentifier($this->getData('href'));
        } else if ($this->getData('page_id')) {
            $this->_anchorText = $this->_resourcePage->getCmsPageTitleById($this->getData('page_id'));
        } else {
            $this->_anchorText = $this->getData('href');
        }

        return $this->_anchorText;
    }
}
