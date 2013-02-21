<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controls configuration factory
 */
class Mage_DesignEditor_Model_Config_Control_Factory
{
    /**
     * File name for quick style tools
     */
    const TYPE_QUICK_STYLES = 'quick-style';

    /**
     * File names with
     * @var array
     */
    protected $_fileNames = array(
        self::TYPE_QUICK_STYLES => 'Mage_DesignEditor::controls/quick_styles.xml'
    );

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /*
     * Initialize dependencies
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Design_Package $package
    ) {
        $this->_objectManager = $objectManager;
        $this->_design = $package;
    }

    /**
     * Get file path by type
     *
     * @param string $type
     * @return string
     * @throws Magento_Exception
     */
    protected function _getFilePathByType($type)
    {
        if (!isset($this->_fileNames[$type])) {
            throw new Magento_Exception("Unknown control configuration type: \"{$type}\"");
        }
        return $this->_design->getFilename($this->_fileNames[$type], array(
            'area' => Mage_Core_Model_Design_Package::DEFAULT_AREA
        ));
    }

    /**
     * Create new instance
     *
     * @param string $type
     * @param array $files
     * @return Mage_DesignEditor_Model_Config_Control_Abstract
     * @throws Magento_Exception
     */
    public function create($type, array $files = array())
    {
        $files[] = $this->_getFilePathByType($type);
        switch ($type) {
            case self::TYPE_QUICK_STYLES:
                return $this->_objectManager->get('Mage_DesignEditor_Model_Config_Control_QuickStyles',
                    array($files));
                break;
            default:
                throw new Magento_Exception("Unknown control configuration type: \"{$type}\"");
                break;
        }
    }
}
