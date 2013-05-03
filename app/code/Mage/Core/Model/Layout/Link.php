<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout Link model class
 *
 * @method int getStoreId()
 * @method int getThemeId()
 * @method int getLayoutUpdateId()
 * @method Mage_Core_Model_Layout_Link setStoreId($id)
 * @method Mage_Core_Model_Layout_Link setThemeId($id)
 * @method Mage_Core_Model_Layout_Link setLayoutUpdateId($id)
 */
class Mage_Core_Model_Layout_Link extends Mage_Core_Model_Abstract
{
    /**
     * Layout Update model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Layout_Link');
    }
}
