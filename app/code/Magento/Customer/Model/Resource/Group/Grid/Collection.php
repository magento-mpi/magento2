<?php
/**
 * Customer group collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource\Group\Grid;

class Collection extends \Magento\Customer\Model\Resource\Group\Collection
{
    /**
     * Resource initialization
     * @return \Magento\Customer\Model\Resource\Group\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addTaxClass();
        return $this;
    }
}
