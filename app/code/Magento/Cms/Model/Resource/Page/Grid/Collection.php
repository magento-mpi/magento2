<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource\Page\Grid;

use Magento\Cms\Model\Resource\Page\Collection as PageCollection;

/**
 * CMS page collection
 */
class Collection extends PageCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_previewFlag = true;
    }
}
