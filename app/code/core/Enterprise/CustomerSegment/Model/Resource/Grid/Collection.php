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
class Enterprise_CustomerSegment_Model_Resource_Grid_Collection extends Enterprise_CustomerSegment_Model_Resource_Segment_Collection
{
    /**
     * Add websites for load
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}