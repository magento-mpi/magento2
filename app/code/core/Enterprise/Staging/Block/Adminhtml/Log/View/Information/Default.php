<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging information renderer
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Log_View_Information_Default extends Mage_Adminhtml_Block_Widget
{
    protected $_mapper;
    protected $_items;

    protected function _construct()
    {
        if ($this->getLog()) {
            $this->getLog()->restoreMap();
            $this->_mapper = $this->getLog()->getStaging()->getMapperInstance();
        }
    }

    /**
     * Retrieve currently viewing log
     *
     * @return Enterprise_Staging_Model_Staging_Log
     */
    public function getLog()
    {
        if (!($this->getData('log') instanceof Enterprise_Staging_Model_Staging_Log)) {
            $this->setData('log', Mage::registry('log'));
        }
        return $this->getData('log');
    }

    /**
     * Prepares array of staging items related to proccess of rollback, create or merge
     *
     * @return array
     */
    public function getItems()
    {
        if (!$this->_items) {
            $stagingItems = $this->_mapper->getStagingItems();
            $items = array();
            if ($stagingItems) {
                foreach ($stagingItems as $code => $item) {
                    $items[$code] = array(
                        'code' => $code,
                        'label' => (string)$item->label
                    );
                }
            } else {
                $items = $this->__('No information available.');
            }
            $this->_items = $items;
        }

        return $this->_items;
    }
}
