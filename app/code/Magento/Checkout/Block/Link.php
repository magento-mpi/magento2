<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Checkout" link
 */
namespace Magento\Checkout\Block;

class Link extends \Magento\Page\Block\Link
{
    /**
     * @var \Magento\Core\Model\ModuleManager
     */
    protected $_moduleManager;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\ModuleManager $moduleManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\ModuleManager $moduleManager,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('checkout', array('_secure' => true));
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helper('Magento\Checkout\Helper\Data')->canOnepageCheckout()
            || !$this->_moduleManager->isOutputEnabled('Magento_Checkout')
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
