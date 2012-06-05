<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A proxy for Fallback model. This proxy processes fallback resolution calls by either using map of cached paths, or
 * passing resolution to the Fallback model.
 */
class Mage_Core_Model_Design_Fallback_Caching_Proxy
{
    /**
     * Fallback map
     *
     * @var Mage_Core_Model_Design_Fallback_Map
     */
    protected $_map;

    /**
     * Proxied fallback model
     *
     * @var Mage_Core_Model_Design_Fallback
     */
    protected $_fallback;

    /**
     * Path to Magento base directory
     *
     * @var string
     */
    protected $_basePath;

    /**
     * Parameters of cached method signatures
     *
     * @var array
     */
    protected $_filePathMethods = array(
        'getFile' => array('file', 'area', 'package', 'theme', 'module'),
        'getLocaleFile' => array('file', 'area', 'package', 'theme', 'locale'),
        'getSkinFile' => array('file', 'area', 'package', 'theme', 'skin', 'locale', 'module')
    );

    public function __construct()
    {
        $this->_basePath = Mage::getBaseDir() . DIRECTORY_SEPARATOR;
    }

    /**
     * Process fallback method calls transparently
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($this->_filePathMethods[$name])) {
            return $this->_getFilePath($name, $arguments);
        }
        return call_user_func_array(array($this->_getFallback(), $name), $arguments);
    }

    /**
     * Returns instance of fallback model. Creates it, if it has not been created yet.
     *
     * @return Mage_Core_Model_Design_Fallback
     */
    protected function _getFallback()
    {
        if (!$this->_fallback) {
            $this->_fallback = Mage::getModel('Mage_Core_Model_Design_Fallback');
        }
        return $this->_fallback;
    }

    /**
     * Returns instance of fallback map model. Creates it, if it has not been created yet.
     *
     * @return Mage_Core_Model_Design_Fallback_Map
     */
    protected function _getMap()
    {
        if (!$this->_map) {
            $this->_map = Mage::getModel(
                'Mage_Core_Model_Design_Fallback_Map',
                Mage::getConfig()->getTempVarDir() . DIRECTORY_SEPARATOR . 'maps' . DIRECTORY_SEPARATOR . 'fallback'
            );
        }
        return $this->_map;
    }

    /**
     * Return path to a file, using either caching map, or original fallback mechanism
     *
     * @param $methodName
     * @param $arguments
     * @return mixed
     */
    protected function _getFilePath($methodName, $arguments)
    {
        // Extract arguments according to method signature
        $file = null;
        $area = null;
        $package = null;
        $theme = null;
        $skin = null;
        $locale = null;
        $module = null;
        $i = 0;
        foreach ($this->_filePathMethods[$methodName] as $paramName) {
            $$paramName = $arguments[$i++];
        }

        // Retrieve cached file path
        $relFilePath = $this->_getMap()->getFilePath($file, $area, $package, $theme, $skin, $locale, $module);
        if ($relFilePath !== null) {
            if ((string) $relFilePath !== '')
            return $this->_basePath . $relFilePath;
        }

        // Pass resolution to original Fallback model
        $fullFilePath = call_user_func_array(array($this->_getFallback(), $methodName), $arguments);
        $this->setFilePath($file, $area, $package, $theme, $skin, $locale, $module, $fullFilePath);
        return $fullFilePath;
    }

    /**
     * Save fallback map
     *
     * @return Mage_Core_Model_Design_Fallback_Caching_Proxy
     */
    public function saveMap()
    {
        $this->_getMap()->save();
        return $this;
    }

    /**
     * Force file resolution path to be $filePath
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string $skin
     * @param string|null $locale
     * @param string|null $module
     * @param string $filePath
     * @return Mage_Core_Model_Design_Fallback_Caching_Proxy
     */
    public function setFilePath($file, $area, $package, $theme, $skin, $locale, $module, $filePath)
    {
        $relFilePath = substr($filePath, strlen($this->_basePath));
        $this->_getMap()->setFilePath($file, $area, $package, $theme, $skin, $locale, $module, $relFilePath);
        return $this;
    }
}
