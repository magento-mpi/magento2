<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource\Page;

use Magento\Cms\Api\Data\PageCollectionInterface;
use Magento\Cms\Model\Resource\AbstractCollection;
use Magento\Cms\Api\Data\PageInterface;

/**
 * CMS page collection
 *
 * Class Collection
 * @package Magento\Cms\Model\Resource\Page
 */
class Collection extends AbstractCollection implements PageCollectionInterface
{
    /**
     * @return void
     */
    protected function init()
    {
        $this->setDataInterfaceName('Magento\Cms\Api\Data\PageInterface');
        $this->storeTableName = 'cms_page_store';
        $this->linkFieldName = 'page_id';
        parent::init();
    }
}
