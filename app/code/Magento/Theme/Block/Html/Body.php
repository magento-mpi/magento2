<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Html;

use \Magento\Framework\View\Element\AbstractBlock;

/**
 * Temporary block to replace some logic from root block
 */
class Body extends AbstractBlock
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\View\Page\Config $pageConfig,
        array $data = array()
    ) {
        $this->pageConfig = $pageConfig;
        parent::__construct($context, $data);
    }

    /**
     * Add body class to page configuration api
     *
     * @param string $bodyClass
     * @return $this
     */
    public function addBodyClass($bodyClass)
    {
        $this->pageConfig->addBodyClass($bodyClass);
        return $this;
    }
}
