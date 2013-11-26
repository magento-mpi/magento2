<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Controller;

class WizardTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var string
     */
    protected static $_tmpDir;

    /**
     * @var array
     */
    protected static $_params = array();

    public static function setUpBeforeClass()
    {
        /** @var \Magento\Filesystem $filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Filesystem');
        $varDirectory = $filesystem->getDirectoryWrite(\Magento\Filesystem::VAR_DIR);
        $tmpDir =  'WizardTest';
        $varDirectory->delete($tmpDir);
        // deliberately create a file instead of directory to emulate broken access to static directory
        $varDirectory->touch($tmpDir);

        self::$_tmpDir = $varDirectory->getAbsolutePath($tmpDir);
    }

    public function testPreDispatch()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->configure(array(
            'preferences' => array(
                'Magento\App\RequestInterface' => 'Magento\TestFramework\Request',
                'Magento\App\Response\Http' => 'Magento\TestFramework\Response'
            )
        ));
        /** @var $appState \Magento\App\State */
        $appState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State');
        $appState->setInstallDate(false);
        $this->dispatch('install/wizard');
        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
        $appState->setInstallDate(date('r', strtotime('now')));
    }
}
