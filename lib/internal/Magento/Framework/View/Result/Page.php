<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Result;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Element\Template;

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
     * @param Template\Context $context
     * @param \Magento\Framework\View\Layout $layout
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param array $data
     * @param $pageType
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\View\Page\Config $pageConfig,
        $pageType,
        array $data = array()
    ) {
        $this->pageConfig = $pageConfig;
        $this->pageType = $pageType;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Page\Config
     */
    public function getConfig()
    {
        return $this->pageConfig;
    }

    /**
     * @param ResponseInterface $response
     */
    public function renderResult(ResponseInterface $response)
    {
        $update = $this->getLayout()->getUpdate();
//        $update->addHandle('default');
//        $update->addHandle($this->pageType);
//        $update->addHandle($this->pageConfig->getPageLayout());
        parent::renderResult($response);
    }
}
