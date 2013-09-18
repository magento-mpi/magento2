<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha block
 *
 * @category   Core
 * @package    Magento_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Captcha\Block;

class Captcha extends \Magento\Core\Block\Template
{
    /**
     * Captcha data
     *
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_captchaData = null;

    /**
     * @param \Magento\Captcha\Helper\Data $captchaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Captcha\Helper\Data $captchaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_captchaData = $captchaData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
        $blockPath = $this->_captchaData->getCaptcha($this->getFormId())->getBlockName();
        $block = $this->getLayout()->createBlock($blockPath);
        $block->setData($this->getData());
        return $block->toHtml();
    }
}
