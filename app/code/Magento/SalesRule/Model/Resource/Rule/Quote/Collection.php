<?php
/**
 * Sales Rules resource collection model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Resource\Rule\Quote;

class Collection extends \Magento\SalesRule\Model\Resource\Rule\Collection
{
    /**
     * Add websites for load
     *
     * @return $this
     */

    public function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
