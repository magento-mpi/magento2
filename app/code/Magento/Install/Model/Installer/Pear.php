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
 * PEAR Packages Download Manager
 */
namespace Magento\Install\Model\Installer;

class Pear extends \Magento\Install\Model\Installer\AbstractInstaller
{
    /**
     * Installer Session
     *
     * @var \Magento\Core\Model\Session\Generic
     */
    protected $_session;

    /**
     * @param \Magento\Install\Model\InstallerProxy $installer
     * @param \Magento\Core\Model\Session\Generic $session
     */
    public function __construct(
        \Magento\Install\Model\InstallerProxy $installer,
        \Magento\Core\Model\Session\Generic $session
    ) {
        parent::__construct($installer);
        $this->_session = $session;
    }


    /**
     * @return array
     */
    public function getPackages()
    {
        $packages = array(
            'pear/PEAR-stable',
            'connect.magentocommerce.com/core/Magento_Pear_Helpers',
            'connect.magentocommerce.com/core/Lib_ZF',
            'connect.magentocommerce.com/core/Lib_Varien',
            'connect.magentocommerce.com/core/Magento_All',
            'connect.magentocommerce.com/core/Interface_Frontend_Default',
            'connect.magentocommerce.com/core/Interface_Adminhtml_Default',
            'connect.magentocommerce.com/core/Interface_Install_Default',
        );
        return $packages;
    }

    /**
     * @return bool
     */
    public function checkDownloads()
    {
        $pear = new \Magento\Pear;
        $pkg = new PEAR_PackageFile($pear->getConfig(), false);
        $result = true;
        foreach ($this->getPackages() as $package) {
            $obj = $pkg->fromAnyFile($package, PEAR_VALIDATE_NORMAL);
            if (PEAR::isError($obj)) {
                $uinfo = $obj->getUserInfo();
                if (is_array($uinfo)) {
                    foreach ($uinfo as $message) {
                        if (is_array($message)) {
                            $message = $message['message'];
                        }
                        $this->_session->addError($message);
                    }
                } else {
                    print_r($obj->getUserInfo());
                }
                $result = false;
            }
        }
        return $result;
    }
}
