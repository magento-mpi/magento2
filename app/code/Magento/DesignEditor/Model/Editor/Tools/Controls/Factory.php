<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controls configuration factory
 */
namespace Magento\DesignEditor\Model\Editor\Tools\Controls;

class Factory
{
    /**#@+
     * Group of types
     */
    const TYPE_QUICK_STYLES = 'quick-style';
    const TYPE_IMAGE_SIZING = 'image-sizing';
    /**#@-*/

    /**
     * File names with
     *
     * @var array
     */
    protected $_fileNames = array(
        self::TYPE_QUICK_STYLES => 'Magento_DesignEditor::controls/quick_styles.xml',
        self::TYPE_IMAGE_SIZING => 'Magento_DesignEditor::controls/image_sizing.xml'
    );

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\View\FileSystem $viewFileSystem
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\View\FileSystem $viewFileSystem
    ) {
        $this->_objectManager = $objectManager;
        $this->_viewFileSystem = $viewFileSystem;
    }

    /**
     * Get file path by type
     *
     * @param string $type
     * @param \Magento\Core\Model\Theme $theme
     * @return string
     * @throws \Magento\Exception
     */
    protected function _getFilePathByType($type, $theme)
    {
        if (!isset($this->_fileNames[$type])) {
            throw new \Magento\Exception("Unknown control configuration type: \"{$type}\"");
        }
        return $this->_viewFileSystem->getFilename($this->_fileNames[$type], array(
            'area'       => \Magento\Core\Model\View\DesignInterface::DEFAULT_AREA,
            'themeModel' => $theme
        ));
    }

    /**
     * Create new instance
     *
     * @param string $type
     * @param \Magento\Core\Model\Theme $theme
     * @param \Magento\Core\Model\Theme $parentTheme
     * @param array $files
     * @return \Magento\DesignEditor\Model\Editor\Tools\Controls\Configuration
     * @throws \Magento\Exception
     */
    public function create(
        $type,
        \Magento\Core\Model\Theme $theme = null,
        \Magento\Core\Model\Theme $parentTheme = null,
        array $files = array()
    ) {
        $files[] = $this->_getFilePathByType($type, $theme);
        switch ($type) {
            case self::TYPE_QUICK_STYLES:
                $class = 'Magento\DesignEditor\Model\Config\Control\QuickStyles';
                break;
            case self::TYPE_IMAGE_SIZING:
                $class = 'Magento\DesignEditor\Model\Config\Control\ImageSizing';
                break;
            default:
                throw new \Magento\Exception("Unknown control configuration type: \"{$type}\"");
                break;
        }
        /** @var $config \Magento\DesignEditor\Model\Config\Control\AbstractControl */
        $config = $this->_objectManager->create($class, array('configFiles' => $files));
        return $this->_objectManager->create(
            'Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration', array(
                'configuration' => $config,
                'theme'         => $theme,
                'parentTheme'   => $parentTheme
        ));
    }
}
