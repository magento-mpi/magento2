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
    protected function _construct()
    {
        $this->setCurrentArea('install');
        parent::_construct();
        $this->_areaDesign = (string)Mage::getConfig()->getNode(
            'install/' . Mage_Core_Model_Design_Package::XML_PATH_THEME
        ) ?: 'default/default/default';

        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
    }
}
