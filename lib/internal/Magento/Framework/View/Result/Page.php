<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\Framework\View;

/**
 * A "page" result that encapsulates page type, page configuration
 * and imposes certain layout handles.
 *
 * The framework convention is that there will be loaded a guaranteed handle for "all pages",
 * then guaranteed handle that corresponds to page type
 * and a guaranteed handle that stands for page layout (a wireframe of a page)
 *
 * Page result is a more specific implementation of a generic layout response
 */
class Page extends Layout
{
    /**
     * @var string
     */
    private $pageType;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    /**
     * @param View\Element\Template\Context $context
     * @param View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param View\Page\Config $pageConfig
     * @param $pageType
     * @param array $data
     */
    public function __construct(
        View\Element\Template\Context $context,
        View\LayoutFactory $layoutFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        View\Page\Config $pageConfig,
        $pageType,
        array $data = array()
    ) {
        $this->pageConfig = $pageConfig;
        $this->pageType = $pageType;
        parent::__construct($context, $layoutFactory, $translateInline, $data);
    }

    /**
     * @return \Magento\Framework\View\Page\Config
     */
    public function getConfig()
    {
        return $this->pageConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function initLayout()
    {
        parent::initLayout();
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $update->addHandle($this->pageType);
        $update->addHandle($this->pageConfig->getPageLayout());
        return $this;
    }
}
