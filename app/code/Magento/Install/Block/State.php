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
     * Install Wizard
     *
     * @var Magento_Install_Model_Wizard
     */
    protected $_wizard;

    /**
     * Core Cookie
     *
     * @var Magento_Core_Model_Cookie
     */
    protected $_cookie;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Install_Model_Wizard $wizard
     * @param Magento_Core_Model_Cookie $cookie
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Install_Model_Wizard $wizard,
        Magento_Core_Model_Cookie $cookie,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_wizard = $wizard;
        $this->_cookie = $cookie;

        $this->assign('steps', $this->_wizard->getSteps());
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
        $session = $this->_cookie->get('magento_downloader_session');
        return $session ? true : false;
    }
}
