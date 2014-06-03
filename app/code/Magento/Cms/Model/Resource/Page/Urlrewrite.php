<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource\Page;

/**
 * Cms page url rewrite resource model
 */
class Urlrewrite extends \Magento\Framework\Model\Resource\Db\AbstractDb
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
