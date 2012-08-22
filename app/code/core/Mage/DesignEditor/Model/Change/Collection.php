<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Visual design editor changes collection
 */
class Mage_DesignEditor_Model_Change_Collection extends Varien_Data_Collection
{
    const ITEM_TYPE = 'Mage_DesignEditor_Model_ChangeAbstract';

    public function getItemClass()
    {
        return self::ITEM_TYPE;
    }
}
