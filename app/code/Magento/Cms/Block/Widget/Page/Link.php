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
 * Widget to display link to CMS page
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Cms\Block\Widget\Page;

class Link
    extends \Magento\Core\Block\Html\Link
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
     * Cms page
     *
     * @var \Magento\Cms\Helper\Page
     */
    protected $_cmsPage = null;

    /**
     * @param \Magento\Cms\Helper\Page $cmsPage
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Helper\Page $cmsPage,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_cmsPage = $cmsPage;
        parent::__construct($coreData, $context, $data);
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
                $this->_title = \Mage::getResourceSingleton('Magento\Cms\Model\Resource\Page')
                    ->getCmsPageTitleById($this->getData('page_id'));
            } else if ($this->getData('href')) {
                $this->_title = \Mage::getResourceSingleton('Magento\Cms\Model\Resource\Page')
                    ->setStore(\Mage::app()->getStore())
                    ->getCmsPageTitleByIdentifier($this->getData('href'));
            }
        }

        return $this->_title;
    }

    /**
     * Prepare anchor text using passed text as parameter.
     * If anchor text was not specified use title instead and
     * if title will be blank string, page identifier will be used.
     *
     * @return string
     */
    public function getAnchorText()
    {
        if ($this->getData('anchor_text')) {
            $this->_anchorText = $this->getData('anchor_text');
        } else if ($this->getTitle()) {
            $this->_anchorText = $this->getTitle();
        } else if ($this->getData('href')) {
            $this->_anchorText = \Mage::getResourceSingleton('Magento\Cms\Model\Resource\Page')
                ->setStore(\Mage::app()->getStore())
                ->getCmsPageTitleByIdentifier($this->getData('href'));
        } else if ($this->getData('page_id')) {
            $this->_anchorText = \Mage::getResourceSingleton('Magento\Cms\Model\Resource\Page')
                ->getCmsPageTitleById($this->getData('page_id'));
        } else {
            $this->_anchorText = $this->getData('href');
        }

        return $this->_anchorText;
    }
}
