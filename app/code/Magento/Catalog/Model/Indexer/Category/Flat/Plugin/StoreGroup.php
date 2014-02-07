<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class StoreGroup extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    protected function validate(\Magento\Core\Model\AbstractModel $group)
    {
        return $group->dataHasChangedFor('root_category_id') && !$group->isObjectNew();
    }
}
