<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Block\Header;

/**
 * Remember Me block
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Additional extends \Magento\View\Element\Html\Link
{
    /**
     * Persistent helper
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Persistent\Helper\Data $persistentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Persistent\Helper\Data $persistentHelper,
        array $data = array()
    ) {
        $this->_persistentHelper = $persistentHelper;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve unset cookie link
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('persistent/index/unsetCookie');
    }

    /**
     * Render additional header html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $text = $this->_persistentHelper->getPersistentName();
        return '<span><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($text) . '</a></span>';
    }
}
