<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block;

use Magento\Framework\View\Element\Template;

/**
 * Html page block
 */
class Html extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * The list of available URLs
     *
     * @var array
     */
    protected $_urls = array();

    /**
     * @var string
     */
    protected $_title = '';

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Add block data
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_urls = array(
            'base' => $this->_storeManager->getStore()->getBaseUrl('web'),
            'baseSecure' => $this->_storeManager->getStore()->getBaseUrl('web', true),
            'current' => $this->_request->getRequestUri()
        );

        $this->pageConfig->addBodyClass($this->_request->getFullActionName('-'));

        if ($this->_cacheState->isEnabled(self::CACHE_GROUP)) {
            $this->_sidResolver->setUseSessionVar(true);
        }
    }

    /**
     * Retrieve base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urls['base'];
    }

    /**
     * Retrieve base secure URL
     *
     * @return mixed
     */
    public function getBaseSecureUrl()
    {
        return $this->_urls['baseSecure'];
    }

    /**
     * Retrieve current URL
     *
     * @return mixed
     */
    public function getCurrentUrl()
    {
        return $this->_urls['current'];
    }

    /**
     * Print Logo URL (Conf -> Sales -> Invoice and Packing Slip Design)
     *
     * @return string
     */
    public function getPrintLogoUrl()
    {
        // load html logo
        $logo = $this->_scopeConfig->getValue(
            'sales/identity/logo_html',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!empty($logo)) {
            $logo = 'sales/store/logo_html/' . $logo;
        }

        // load default logo
        if (empty($logo)) {
            $logo = $this->_scopeConfig->getValue(
                'sales/identity/logo',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if (!empty($logo)) {
                // prevent tiff format displaying in html
                if (strtolower(substr($logo, -5)) === '.tiff' || strtolower(substr($logo, -4)) === '.tif') {
                    $logo = '';
                } else {
                    $logo = 'sales/store/logo/' . $logo;
                }
            }
        }

        // buld url
        if (!empty($logo)) {
            $logo = $this->_urlBuilder
                    ->getBaseUrl(array('_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA)) . $logo;
        } else {
            $logo = '';
        }

        return $logo;
    }

    /**
     * Retrieve logo text for print page
     *
     * @return string
     */
    public function getPrintLogoText()
    {
        return $this->_scopeConfig->getValue(
            'sales/identity/address',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Set header title
     *
     * @param string $title
     * @return $this
     */
    public function setHeaderTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Retrieve header title
     *
     * @return string
     */
    public function getHeaderTitle()
    {
        return $this->_title;
    }

    /**
     * Retrieve base language
     *
     * @return string
     */
    public function getLang()
    {
        if (!$this->hasData('lang')) {
            $this->setData('lang', substr($this->_localeResolver->getLocaleCode(), 0, 2));
        }
        return $this->getData('lang');
    }

    /**
     * Retrieve absolute footer html
     *
     * @return string
     */
    public function getAbsoluteFooter()
    {
        return $this->_scopeConfig->getValue(
            'design/footer/absolute_footer',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        if ($this->_cacheState->isEnabled(self::CACHE_GROUP)) {
            $this->_sidResolver->setUseSessionVar(false);
            \Magento\Framework\Profiler::start('CACHE_URL');
            $html = $this->_urlBuilder->sessionUrlVar($html);
            \Magento\Framework\Profiler::stop('CACHE_URL');
        }
        return $html;
    }
}
