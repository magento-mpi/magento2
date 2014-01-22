<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block;

/**
 * Html page block
 */
class Html extends \Magento\View\Element\Template
{
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
     * Add block data
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_urls = array(
            'base'      => $this->_storeManager->getStore()->getBaseUrl('web'),
            'baseSecure'=> $this->_storeManager->getStore()->getBaseUrl('web', true),
            'current'   => $this->_request->getRequestUri()
        );

        $this->addBodyClass($this->_request->getFullActionName('-'));

        if ($this->_cacheState->isEnabled(self::CACHE_GROUP)) {
            $this->_app->setUseSessionVar(true);
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
        $logo = $this->_storeConfig->getConfig('sales/identity/logo_html');
        if (!empty($logo)) {
            $logo = 'sales/store/logo_html/' . $logo;
        }

        // load default logo
        if (empty($logo)) {
            $logo = $this->_storeConfig->getConfig('sales/identity/logo');
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
            $logo = $this->_urlBuilder->getBaseUrl(array('_type' => \Magento\UrlInterface::URL_TYPE_MEDIA)) . $logo;
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
        return $this->_storeConfig->getConfig('sales/identity/address');
    }

    /**
     * Set header title
     *
     * @param string $title
     * @return \Magento\Theme\Block\Html
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
     * Add CSS class to page body tag
     *
     * @param string $className
     * @return \Magento\Theme\Block\Html
     */
    public function addBodyClass($className)
    {
        $className = preg_replace('#[^a-z0-9]+#', '-', strtolower($className));
        $this->setBodyClass($this->getBodyClass() . ' ' . $className);
        return $this;
    }

    /**
     * Retrieve base language
     *
     * @return string
     */
    public function getLang()
    {
        if (!$this->hasData('lang')) {
            $this->setData('lang', substr($this->_locale->getLocaleCode(), 0, 2));
        }
        return $this->getData('lang');
    }

    /**
     * Retrieve body class
     *
     * @return string
     */
    public function getBodyClass()
    {
        return $this->_getData('body_class');
    }

    /**
     * Retrieve absolute footer html
     *
     * @return string
     */
    public function getAbsoluteFooter()
    {
        return $this->_storeConfig->getConfig('design/footer/absolute_footer');
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
            $this->_app->setUseSessionVar(false);
            \Magento\Profiler::start('CACHE_URL');
            $html = $this->_urlBuilder->sessionUrlVar($html);
            \Magento\Profiler::stop('CACHE_URL');
        }
        return $html;
    }
}
