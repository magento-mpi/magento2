<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Cms\Model\Resource\Block;

use Magento\Cms\Api\Data\BlockCollectionInterface;
use Magento\Cms\Model\Resource\AbstractCollection;

/**
 * CMS block collection
 *
 * Class Collection
 */
class Collection extends AbstractCollection implements BlockCollectionInterface
{
    /**
     * @return void
     */
    protected function init()
    {
        $this->setDataInterfaceName('Magento\Cms\Api\Data\BlockInterface');
        $this->storeTableName = 'cms_block_store';
        $this->linkFieldName = 'block_id';
        parent::init();
    }
}
