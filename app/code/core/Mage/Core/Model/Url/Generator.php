<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * URL Generator model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Url_Generator
{
    /**
     * Get base URL path by type
     *
     * @param string $type
     * @param null|bool $secure
     * @return string
     */
    public function getBaseUrl($type = Mage_Core_Model_Store::URL_TYPE_LINK, $secure = null)
    {
        return Mage::getBaseUrl($type, $secure);
    }
}
