<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once "Varien/Pear/Package.php";

/**
 * PEAR Packages Download Manager
 *
 * @category   Mage
 * @package    Mage_Install
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Install_Model_Installer_Pear
{
    public function getPackages()
    {
        $packages = array(
            'var-dev/Mage_Pear_Helpers',
            'var-dev/Mage_All',
            'var-dev/Interface_Frontend_Default',
            'var-dev/Interface_Adminhtml_Default'
        );
        return $packages;
    }

    public function checkDownloads()
    {
        $pear = new Varien_Pear;
        $pkg = new PEAR_PackageFile($pear->getConfig(), false);
        $result = true;
        foreach ($this->getPackages() as $package) {
            $obj = $pkg->fromAnyFile($package);
            if (PEAR::isError($obj)) {
                $uinfo = $obj->getUserInfo();
                foreach ($uinfo as $message) {
                    if (is_array($message)) {
                        $message = $message['message'];
                    }
                    Mage::getSingleton('install/session')->addError($message);
                }
                $result = false;
            }
        }
        return $result;
    }

    public function installPackages()
    {
        ob_implicit_flush();

        $pear = new Varien_Pear;
        $fe = $pear->getFrontend();
        $fe->setLogStream('stdout');
?>
<html><head><style>
body { margin:0px; padding:3px; background:black; }
pre { font:normal 11px Courier New, serif; color:#2EC029; }
</style></head><body><pre>
<?
        $result = $pear->run('install', array('onlyreqdeps'=>1, 'force'=>1), $this->getPackages());
        #$result = $pear->run('install', array('onlyreqdeps'=>1, 'force'=>1), array('PEAR'));
        #$result = $pear->run('help', array(), array());
        #$result = true;
        if ($result instanceof PEAR_Error) {
            echo "\r\n\r\nPEAR ERROR: ".$result->getMessage();
        }
#        print_r($result);
        #print_r($fe->getLog());
        #print_r($fe->getOutput());
?>
</pre><script type="text/javascript">
<?
        if ($result instanceof PEAR_Error) {
            echo 'parent.installFailure()';
        } else {
            echo 'parent.installSuccess()';
        }
?>
</script></body></html>
<?
    }
}