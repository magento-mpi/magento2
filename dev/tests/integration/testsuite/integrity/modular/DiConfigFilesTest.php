<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Modular_DiConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Primary DI configs from app/etc
     * @var array
     */
    protected static $_primaryFiles = array();

    /**
     * Global DI configs from all modules
     * @var array
     */
    protected static $_moduleGlobalFiles = array();

    /**
     * Area DI configs from all modules
     * @var array
     */
    protected static $_moduleAreaFiles = array();

    protected function _prepareFiles()
    {
        //init primary configs
        /** @var $dir Mage_Core_Model_Dir */
        $dir = Mage::getObjectManager()->get('Mage_Core_Model_Dir');

        $configPath = $dir->getDir(Mage_Core_Model_Dir::APP) . DS . 'etc' . DS . 'di';
        self::$_primaryFiles = glob($configPath . DS. '*.xml');

        //init module global configs
        /** @var $modulesReader Mage_Core_Model_Config_Modules_Reader */
        $modulesReader = Mage::getObjectManager()->get('Mage_Core_Model_Config_Modules_Reader');
        self::$_moduleGlobalFiles = $modulesReader->getModuleConfigurationFiles('di.xml');

        //init module area configs
        $areas = array('adminhtml', 'frontend');
        foreach ($areas as $area) {
            $moduleAreaFiles = $modulesReader->getModuleConfigurationFiles($area . DS . 'di.xml');
            self::$_moduleAreaFiles[$area] = $moduleAreaFiles;
        }
    }

    /**
     * @param string $file
     * @return void
     * @dataProvider linearFilesProvider
     */
    public function testDiConfigFileWithoutMerging($file)
    {
        /** @var $reader Magento_ObjectManager_Config_Reader_Dom */
        $reader = $this->getMock('Magento_ObjectManager_Config_Reader_Dom', array('_merge'), array(), '', false);
        $xsd = $reader->getSchemaFile();

        $dom = new DOMDocument();
        $dom->load($file);
        if (!@$dom->schemaValidate($xsd)) {
            $this->fail('File ' . $file . ' has invalid xml structure.');
        }
    }

    public function linearFilesProvider()
    {
        if (empty(self::$_primaryFiles)) {
            $this->_prepareFiles();
        }

        $common = array_merge(self::$_primaryFiles, self::$_moduleGlobalFiles);

        foreach (self::$_moduleAreaFiles as $files) {
            $common = array_merge($common, $files);
        }

        $output = array();
        foreach ($common as $file) {
            $output[$file] = array($file);
        }

        return $output;
    }

    /**
     * @param array $files
     * @dataProvider mixedFilesProvider
     */
    public function testMergedDiConfig(array $files)
    {
        $mapper = $this->getMock('Magento_ObjectManager_Config_Mapper_Dom', array(), array(), '', false);
        new Magento_ObjectManager_Config_Reader_Dom($files, $mapper, true);
    }

    public function mixedFilesProvider()
    {
        if (empty(self::$_primaryFiles)) {
            $this->_prepareFiles();
        }
        foreach (self::$_primaryFiles as $file) {
            $primaryFiles[$file] = array(array($file));
        }
        $primaryFiles['all primary config files'] = array(self::$_primaryFiles);

        foreach (self::$_moduleGlobalFiles as $file) {
            $moduleFiles[$file] = array(array($file));
        }
        $moduleFiles['all module global config files'] = array(self::$_moduleGlobalFiles);

        $areaFiles = array();
        foreach (self::$_moduleAreaFiles as $area => $files) {
            foreach ($files as $file) {
                $areaFiles[$file] = array(array($file));
            }
            $areaFiles["all $area config files"] = array(self::$_moduleAreaFiles[$area]);
        }

        return $primaryFiles + $moduleFiles + $areaFiles;
    }
}
