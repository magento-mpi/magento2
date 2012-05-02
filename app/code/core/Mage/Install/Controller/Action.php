<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Install_Controller_Action extends Mage_Core_Controller_Varien_Action
{
    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'install';

    protected function _construct()
    {
        parent::_construct();
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
    }
}
