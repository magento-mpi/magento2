<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Block;

/**
 * Block for Urlrewrites grid container
 */
class GridContainer extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Part for generating apropriate grid block name
     *
     * @var string
     */
    protected $_controller = 'urlredirect';

    /**
     * @var \Magento\UrlRedirect\Block\Selector
     */
    protected $_urlrewriteSelector;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param Selector $urlrewriteSelector
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\UrlRedirect\Block\Selector $urlrewriteSelector,
        array $data = array()
    ) {
        $this->_urlrewriteSelector = $urlrewriteSelector;
        parent::__construct($context, $data);
    }

    /**
     * Set custom labels and headers
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_headerText = __('URL Rewrite Management');
        $this->_addButtonLabel = __('Add URL Rewrite');
        parent::_construct();
    }

    /**
     * Customize grid row URLs
     *
     * @return string
     */
    public function getCreateUrl()
    {
        $url = $this->getUrl('adminhtml/*/edit');

        $selectorBlock = $this->getSelectorBlock();
        if ($selectorBlock === null) {
            $selectorBlock = $this->_urlrewriteSelector;
        }

        if ($selectorBlock) {
            $modes = array_keys($selectorBlock->getModes());
            $url .= reset($modes);
        }

        return $url;
    }
}
