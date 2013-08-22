<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Install state block
 *
 * @category   Magento
 * @package    Magento_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Install_Block_State extends Magento_Core_Block_Template
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
        $this->assign('steps', Mage::getSingleton('Magento_Install_Model_Wizard')->getSteps());
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
                __('Welcome'),
                __('Validation'),
                __('Magento Connect Manager Deployment'),
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
