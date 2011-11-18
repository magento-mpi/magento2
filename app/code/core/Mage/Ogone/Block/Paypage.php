<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Ogone_Block_Paypage extends Mage_Core_Block_Template
{
    /**
     * Init pay page block
     *
     * @return Mage_Ogone_Block_Paypage
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypage.phtml');
        return $this;
    }
}
