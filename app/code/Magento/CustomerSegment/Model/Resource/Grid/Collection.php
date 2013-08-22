<?php
/**
 * {license_notice}
 *
 * @category    Enterise
 * @package     Enterpise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer segment data grid collection
 *
 * @category    Enterise
 * @package     Enterpise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Model_Resource_Grid_Collection
    extends Magento_CustomerSegment_Model_Resource_Segment_Collection
{
    /**
     * Add websites for load
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract|Magento_CustomerSegment_Model_Resource_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
