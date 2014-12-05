<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource;

/**
 * Class PageCriteriaMapper
 */
class PageCriteriaMapper extends CmsCriteriaMapper
{
    /**
     * @inheritdoc
     */
    protected function init()
    {
        $this->storeTableName = 'cms_page_store';
        $this->linkFieldName = 'page_id';
        $this->initResource('Magento\Cms\Model\Resource\Page');
        parent::init();
    }
}
