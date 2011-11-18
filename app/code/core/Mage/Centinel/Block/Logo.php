<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Centinel payment form logo block
 */
class Mage_Centinel_Block_Logo extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('logo.phtml');
    }

    /**
     * Return code of payment method
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getMethod()->getCode();
    }
}
