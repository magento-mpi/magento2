<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource\Block;

use Magento\Cms\Api\Data\BlockCollectionInterface;
use Magento\Cms\Model\Resource\AbstractCollection;
use Magento\Cms\Api\Data\BlockInterface;

/**
 * CMS block collection
 *
 * Class Collection
 * @package Magento\Cms\Model\Resource\Block
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
