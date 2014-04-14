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
 * Captcha block for adminhtml area
 *
 * @category   Core
 * @package    Magento_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Captcha\Block\Adminhtml\Captcha;

class DefaultCaptcha extends \Magento\Captcha\Block\Captcha\DefaultCaptcha
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Captcha\Helper\Data $captchaData
     * @param \Magento\Backend\Model\UrlInterface $url
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Captcha\Helper\Data $captchaData,
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Backend\App\ConfigInterface $config,
        array $data = array()
    ) {
        parent::__construct($context, $captchaData, $data);
        $this->_url = $url;
        $this->_config = $config;
    }

    /**
     * Returns URL to controller action which returns new captcha image
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        return $this->_url->getUrl(
            'adminhtml/refresh/refresh',
            array('_secure' => $this->_config->isSetFlag('web/secure/use_in_adminhtml'), '_nosecret' => true)
        );
    }
}
