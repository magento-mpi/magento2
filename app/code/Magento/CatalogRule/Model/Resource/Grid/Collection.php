<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\CatalogRule\Model\Resource\Grid;

use Magento\Core\Model\Resource\Db\Collection\AbstractCollection;

class Collection extends \Magento\CatalogRule\Model\Resource\Rule\Collection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();

        return $this;
    }
}
