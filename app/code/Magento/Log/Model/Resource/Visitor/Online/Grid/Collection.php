<?php
/**
 * Log Online visitors collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\Resource\Visitor\Online\Grid;

class Collection extends \Magento\Log\Model\Resource\Visitor\Online\Collection
{
    /**
     *
     * @var array
     * @return \Magento\Log\Model\Resource\Visitor\Online\Grid\Collection
     */

    protected function _initSelect()
    {
        parent::_initSelect();
        \Mage::getModel('\Magento\Log\Model\Visitor\Online')
           ->prepare();
        $this->addCustomerData();
        return $this;
    }

}
