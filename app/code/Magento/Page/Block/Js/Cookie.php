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

class Cookie extends \Magento\Core\Block\Template
{
    /**
     * Get cookie model instance
     *
     * @return \Magento\Core\Model\Cookie
     */
    public function getCookie()
    {
        return \Mage::getSingleton('Magento\Core\Model\Cookie');
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
