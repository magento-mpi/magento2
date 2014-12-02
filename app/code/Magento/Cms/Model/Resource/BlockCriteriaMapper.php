<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource;

/**
 * Class BlockCriteriaMapper
 */
class BlockCriteriaMapper extends CmsCriteriaMapper
{
    /**
     * @inheritdoc
     */
    protected function init()
    {
        $this->storeTableName = 'cms_block_store';
        $this->linkFieldName = 'block_id';
        $this->initResource('Magento\Cms\Model\Resource\Block');
        parent::init();
    }
}
