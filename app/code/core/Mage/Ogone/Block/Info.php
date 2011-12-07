<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone payment iformation block
 */
class Mage_Ogone_Block_Info extends Mage_Payment_Block_Info_Cc
{
    /**
     * Init ogone payment information block to use on admin area
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('info.phtml');
    }
}
