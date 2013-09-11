<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax Calculation Collection
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Resource\Calculation\Grid;

class Collection extends \Magento\Tax\Model\Resource\Calculation\Rate\Collection
{
    /**
     * Join Region Table
     *
     * @return string
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->joinRegionTable();
        return $this;
    }
}
