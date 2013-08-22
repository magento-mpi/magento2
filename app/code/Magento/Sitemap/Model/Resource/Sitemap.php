<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap resource model
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sitemap_Model_Resource_Sitemap extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('sitemap', 'sitemap_id');
    }
}
