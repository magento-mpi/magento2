<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block\Js;

class Cookie extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Core\Model\Cookie
     */
    protected $_cookie;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Cookie $cookie
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Cookie $cookie,
        array $data = array()
    ) {
        $this->_cookie = $cookie;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Get cookie model instance
     *
     * @return \Magento\Core\Model\Cookie
     */
    public function getCookie()
    {
        return $this->_cookie;
    }
    /**
     * Get configured cookie domain
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->getCookie()->getDomain();
        if (!empty($domain[0]) && ($domain[0] !== '.')) {
            $domain = '.'.$domain;
        }
        return $domain;
    }

    /**
     * Get configured cookie path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getCookie()->getPath();
    }
}
