<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms page url rewrite resource model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Model\Resource\Page;

class Urlrewrite extends \Magento\Core\Model\Resource\Db\AbstractDb
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
