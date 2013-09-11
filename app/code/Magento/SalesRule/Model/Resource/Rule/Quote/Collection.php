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
     * @return Magento_SalesRule_Model_Resource_Rule_Quote_GridCollection
     */

    public function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }

}
