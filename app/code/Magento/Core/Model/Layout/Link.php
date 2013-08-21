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
 * Layout Link model class
 *
 * @method int getStoreId()
 * @method int getThemeId()
 * @method int getLayoutUpdateId()
 * @method Magento_Core_Model_Layout_Link setStoreId($id)
 * @method Magento_Core_Model_Layout_Link setThemeId($id)
 * @method Magento_Core_Model_Layout_Link setLayoutUpdateId($id)
 */
class Magento_Core_Model_Layout_Link extends Magento_Core_Model_Abstract
{
    /**
     * Layout Update model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Resource_Layout_Link');
    }
}
