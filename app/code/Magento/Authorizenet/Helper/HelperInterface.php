<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Authorizenet Data Helper
 *
 * @category   Magento
 * @package    Magento_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Authorizenet\Helper;

interface HelperInterface
{
    /**
     * Retrieve place order url
     *
     * @param array params
     * @return  string
     */
    public function getSuccessOrderUrl($params);

    /**
     * Retrieve redirect ifrmae url
     *
     * @param array params
     * @return string
     */
    public function getRedirectIframeUrl($params);
}
