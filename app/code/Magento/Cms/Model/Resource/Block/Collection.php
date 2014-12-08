<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource\Block;

use Magento\Cms\Model\Resource\AbstractCollection;

/**
 * CMS block collection
 *
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function init()
    {
        $this->setDataInterfaceName('Magento\Cms\Model\Block');
        $this->storeTableName = 'cms_block_store';
        $this->linkFieldName = 'block_id';
        parent::init();
    }
}
