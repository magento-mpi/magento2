<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Captcha\Block\Captcha;

/**
 * Captcha block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class DefaultCaptcha extends \Magento\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'default.phtml';

    /**
     * @var string
     */
    protected $_captcha;

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_captchaData;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Captcha\Helper\Data $captchaData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Captcha\Helper\Data $captchaData,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_captchaData = $captchaData;
    }

    /**
     * Returns template path
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->getIsAjax() ? '' : $this->_template;
    }

    /**
     * Returns URL to controller action which returns new captcha image
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        $store = $this->_storeManager->getStore();
        return $store->getUrl('captcha/refresh', array('_secure' => $store->isCurrentlySecure()));
    }

    /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getCaptchaModel()->isRequired()) {
            $this->getCaptchaModel()->generate();
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Returns captcha model
     *
     * @return \Magento\Captcha\Model\ModelInterface
     */
    public function getCaptchaModel()
    {
        return $this->_captchaData->getCaptcha($this->getFormId());
    }
}
