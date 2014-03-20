<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource\Page;

/**
 * Cms page url rewrite resource model
 */
class Urlrewrite extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Init cms page urlrewrite model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cms_url_rewrite', 'cms_rewrite_id');
    }
}
