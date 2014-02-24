<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Theme\Grid;

/**
 * Theme grid collection
 */
class Collection extends \Magento\Core\Model\Resource\Theme\Collection
{
    /**
     * Add area filter
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->filterVisibleThemes()->addAreaFilter(\Magento\Core\Model\App\Area::AREA_FRONTEND)->addParentTitle();
        return $this;
    }
}
