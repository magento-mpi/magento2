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
     * @var Magento_Captcha_Helper_Data
     */
    protected $_captchaData = null;

    /**
     * @param Magento_Captcha_Helper_Data $captchaData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Captcha_Helper_Data $captchaData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
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
