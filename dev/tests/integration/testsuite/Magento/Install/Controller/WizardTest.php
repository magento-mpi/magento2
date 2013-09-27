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

class Magento_Install_Controller_WizardTest extends Magento_TestFramework_TestCase_ControllerAbstract
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
        $tmpDir =
            Magento_TestFramework_Helper_Bootstrap::getInstance()->getAppInstallDir() . DIRECTORY_SEPARATOR . __CLASS__;
        if (is_file($tmpDir)) {
            unlink($tmpDir);
        } elseif (is_dir($tmpDir)) {
            Magento_Io_File::rmdirRecursive($tmpDir);
        }
        // deliberately create a file instead of directory to emulate broken access to static directory
        touch($tmpDir);
        self::$_tmpDir = $tmpDir;
    }

    public function testPreDispatch()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->configure(array(
            'preferences' => array(
                'Magento_Core_Controller_Request_Http' => 'Magento_TestFramework_Request',
                'Magento_Core_Controller_Response_Http' => 'Magento_TestFramework_Response'
            )
        ));
        /** @var $appState Magento_Core_Model_App_State */
        $appState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App_State');
        $appState->setInstallDate(false);
        $this->dispatch('install/wizard');
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
        $appState->setInstallDate(date('r', strtotime('now')));
    }

    /**
     * @param string $action
     * @dataProvider actionsDataProvider
     * @expectedException Magento_BootstrapException
     */
    public function testPreDispatchImpossibleToRenderPage($action)
    {
        $params = self::$_params;
        $params[Magento_Core_Model_App::PARAM_APP_DIRS][Magento_Core_Model_Dir::STATIC_VIEW] = self::$_tmpDir;
        Magento_TestFramework_Helper_Bootstrap::getInstance()->reinitialize($params);
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->configure(array(
            'preferences' => array(
                'Magento_Core_Controller_Request_Http' => 'Magento_TestFramework_Request',
                'Magento_Core_Controller_Response_Http' => 'Magento_TestFramework_Response'
            )
        ));
        $this->dispatch("install/wizard/{$action}");
    }

    /**
     * @return array
     */
    public function actionsDataProvider()
    {
        return array(
            array('index'),
            array('begin'),
            array('beginPost'),
            array('locale'),
            array('localeChange'),
            array('localePost'),
            array('download'),
            array('downloadPost'),
            array('downloadAuto'),
            array('install'),
            array('downloadManual'),
            array('config'),
            array('configPost'),
            array('installDb'),
            array('administrator'),
            array('administratorPost'),
            array('end'),
        );
    }
}
