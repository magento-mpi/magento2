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
namespace Magento\Captcha\Block\Captcha;

class DefaultCaptcha extends \Magento\View\Block\Template
{
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
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Captcha\Helper\Data $captchaData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Captcha\Helper\Data $captchaData,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
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
        $urlPath = 'captcha/refresh';
        $params = array('_secure' => $this->_storeManager->getStore()->isCurrentlySecure());

        if ($this->_storeManager->getStore()->isAdmin()) {
            $urlPath = 'adminhtml/refresh/refresh';
            $params = array_merge($params, array('_nosecret' => true));
        }

        return $this->_storeManager->getStore()->getUrl($urlPath, $params);
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
