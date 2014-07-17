<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller\Catalog;

class Category extends \Magento\Rss\Controller\Catalog
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_genericAction('category');
    }
}
