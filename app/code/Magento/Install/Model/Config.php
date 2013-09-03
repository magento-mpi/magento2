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
 * Install config
 *
 * @category   Magento
 * @package    Magento_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Install_Model_Config extends \Magento\Simplexml\Config
{
    /**
     * Wizard steps path
     */
    const XML_PATH_WIZARD_STEPS     = 'wizard/steps';

    /**
     * Path to filesystem check writable list
     */
    const XML_PATH_CHECK_WRITEABLE  = 'check/filesystem/writeable';

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $configReader
     */
    public function __construct(Magento_Core_Model_Config_Modules_Reader $configReader)
    {
        parent::__construct();
        $this->loadString('<?xml version="1.0"?><config></config>');
        $configReader->loadModulesConfiguration('install.xml', $this);
    }

    /**
     * Get array of wizard steps
     *
     * array($index => \Magento\Object)
     *
     * @return array
     */
    public function getWizardSteps()
    {
        $steps = array();
        foreach ((array)$this->getNode(self::XML_PATH_WIZARD_STEPS) as $stepName => $step) {
            $stepObject = new \Magento\Object((array)$step);
            $stepObject->setName($stepName);
            $steps[] = $stepObject;
        }
        return $steps;
    }

    /**
     * Retrieve writable path for checking
     *
     * array(
     *      ['writeable'] => array(
     *          [$index] => array(
     *              ['path']
     *              ['recursive']
     *          )
     *      )
     * )
     *
     * @deprecated since 1.7.1.0
     *
     * @return array
     */
    public function getPathForCheck()
    {
        $res = array();

        $items = (array)$this->getNode(self::XML_PATH_CHECK_WRITEABLE);

        foreach ($items as $item) {
            $res['writeable'][] = (array)$item;
        }

        return $res;
    }

    /**
     * Retrieve writable full paths for checking
     *
     * @return array
     */
    public function getWritableFullPathsForCheck()
    {
        $paths = array();
        $items = (array)$this->getNode(self::XML_PATH_CHECK_WRITEABLE);
        foreach ($items as $nodeKey => $item) {
            $value = (array)$item;
            $value['path'] = Mage::getBaseDir($nodeKey);
            $paths[$nodeKey] = $value;
        }

        return $paths;
    }
}
