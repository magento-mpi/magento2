<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme grid collection
 */
namespace Magento\Core\Model\Resource\Theme\Grid;

class Collection extends \Magento\Core\Model\Resource\Theme\Collection
{
    /**
     * Add area filter
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection|
     *  \Magento\Core\Model\Resource\Theme\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->filterVisibleThemes()->addAreaFilter(\Magento\Core\Model\App\Area::AREA_FRONTEND)->addParentTitle();
        return $this;
    }
}
