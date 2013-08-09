<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Install state block
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_State extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'state.phtml';

    /**
     * Assign steps
     */
    protected function _construct()
    {
        $this->assign('steps', Mage::getSingleton('Mage_Install_Model_Wizard')->getSteps());
    }

    /**
     * Get previous downloader steps
     *
     * @return array
     */
    public function getDownloaderSteps()
    {
        if ($this->isDownloaderInstall()) {
            $steps = array(
                Mage::helper('Mage_Install_Helper_Data')->__('Welcome'),
                Mage::helper('Mage_Install_Helper_Data')->__('Validation'),
                Mage::helper('Mage_Install_Helper_Data')->__('Magento Connect Manager Deployment'),
            );
            return $steps;
        } else {
            return array();
        }
    }

    /**
     * Checks for Magento Connect Manager installation method
     *
     * @return bool
     */
    public function isDownloaderInstall()
    {
        $session = Mage::app()->getCookie()->get('magento_downloader_session');
        return $session ? true : false;
    }
}
