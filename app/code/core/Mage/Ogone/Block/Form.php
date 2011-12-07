<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Ogone_Block_Form extends Mage_Payment_Block_Form_Cc
{
    /**
     * Init Ofone pay from to use it on frontend
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('form.phtml');
    }
}
