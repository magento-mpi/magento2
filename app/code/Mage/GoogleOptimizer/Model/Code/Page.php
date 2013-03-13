<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Optimizer Cms Page Model
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Model_Code_Page extends Mage_GoogleOptimizer_Model_Code
{
    const PAGE_TYPE_VARIANT = 'variant';
    protected $_entityType = 'page';

    protected function _afterLoad()
    {
        if ($data = $this->getAdditionalData()) {
            $data = unserialize($data);
            if (isset($data['page_type'])) {
                $this->setPageType($data['page_type']);
            }
        }
        return parent::_afterLoad();
    }

    protected function _beforeSave()
    {

        if ($pageType = $this->getData('page_type')) {
            $this->setData('additional_data', serialize(array(
                'page_type' => $pageType))
            );
        }
        parent::_beforeSave();
    }

    protected function _validate()
    {
        if ($this->getPageType() && $this->getPageType() == self::PAGE_TYPE_VARIANT) {
            if ($this->getTrackingScript()) {
                return true;
            }
        }
        return parent::_validate();
    }

}
