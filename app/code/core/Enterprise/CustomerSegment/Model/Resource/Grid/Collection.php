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
     * Prepare select for load
     *
     * @param Varien_Db_Select $select
     * @return string
     */
    protected function _prepareSelect(Varien_Db_Select $select)
    {
        $this->addWebsitesToResult();
        return parent::_prepareSelect($select);
    }
}