<?php
/**
 * Google Optimizer Page Block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Block\Code;

class Page extends \Magento\GoogleOptimizer\Block\CodeAbstract
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_page;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\GoogleOptimizer\Helper\Data $helper
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\GoogleOptimizer\Helper\Code $codeHelper
     * @param \Magento\Cms\Model\Page $page
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\GoogleOptimizer\Helper\Data $helper,
        \Magento\Core\Model\Registry $registry,
        \Magento\GoogleOptimizer\Helper\Code $codeHelper,
        \Magento\Cms\Model\Page $page,
        array $data = array()
    ) {
        // \Magento\Cms\Model\Page is singleton
        $this->_page = $page;
        parent::__construct($context, $helper, $registry, $codeHelper, $data);
    }

    /**
     * Get cms page entity
     *
     * @return \Magento\Cms\Model\Page
     */
    protected function _getEntity()
    {
        return $this->_page;
    }
}
