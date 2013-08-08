<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms page url rewrite resource model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Resource_Page_Urlrewrite extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Init cms page urlrewrite model
     *
     */
    protected function _construct()
    {
        $this->_init('cms_url_rewrite', 'cms_rewrite_id');
    }
}
