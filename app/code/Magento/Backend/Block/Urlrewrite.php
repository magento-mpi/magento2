<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block;

/**
 * Block for Urlrewrites grid container
 *
 * @method \Magento\Backend\Block\Urlrewrite setSelectorBlock(\Magento\Backend\Block\Urlrewrite\Selector $value)
 * @method null|\Magento\Backend\Block\Urlrewrite\Selector getSelectorBlock()
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Urlrewrite extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Part for generating apropriate grid block name
     *
     * @var string
     */
    protected $_controller = 'urlrewrite';

    /**
     * @var \Magento\Backend\Block\Urlrewrite\Selector
     */
    protected $_urlrewriteSelector;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Block\Urlrewrite\Selector $urlrewriteSelector
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Block\Urlrewrite\Selector $urlrewriteSelector,
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
     * @see \Magento\Backend\Block\Urlrewrite\Selector
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
