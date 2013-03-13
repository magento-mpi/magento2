<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Products Tags grid collection
 *
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Resource_Reports_Product_Grid_Collection extends Mage_Tag_Model_Resource_Reports_Product_Collection
{
    /**
     * @var Mage_Tag_Model_Tag
     */
    protected $_model;

    /**
     * @param Mage_Tag_Model_Tag $tagModel
     * @param null $resource
     */
    public function __construct(Mage_Tag_Model_Tag $tagModel, $resource = null)
    {
        $this->_model = $tagModel;
        parent::__construct($resource);
    }

    /**
     * @return Mage_Tag_Model_Resource_Product_Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addUniqueTagedCount()
            ->addAllTagedCount()
            ->addStatusFilter($this->_model->getApprovedStatus())
            ->addGroupByProduct();
        return $this;

    }
}
