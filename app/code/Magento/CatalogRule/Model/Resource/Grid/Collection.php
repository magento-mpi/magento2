<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\CatalogRule\Model\Resource\Grid;

class Collection
    extends \Magento\CatalogRule\Model\Resource\Rule\Collection
{
    /**
     * @return $this|\Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();

        return $this;
    }
}
