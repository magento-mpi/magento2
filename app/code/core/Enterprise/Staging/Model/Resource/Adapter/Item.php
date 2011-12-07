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
 * Staging item resource adapter
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Adapter_Item extends Enterprise_Staging_Model_Resource_Adapter_Abstract
{
    /**
     * Create run
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Item
     */
    public function createRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        parent::createRun($staging, $event);

        $stagingItems = $staging->getMapperInstance()->getStagingItems();
        foreach ($stagingItems as $stagingItem) {
            $item = Mage::getModel('Enterprise_Staging_Model_Staging_Item')
                ->loadFromXmlStagingItem($stagingItem);
            $staging->addItem($item);
        }

        $staging->saveItems();

        return $this;
    }
}
