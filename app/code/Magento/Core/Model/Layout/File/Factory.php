<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory that produces layout file instances
 */
class Magento_Core_Model_Layout_File_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a layout file
     *
     * @param string $filename
     * @param string $module
     * @param Magento_Core_Model_ThemeInterface $theme
     * @return Magento_Core_Model_Layout_File
     */
    public function create($filename, $module, Magento_Core_Model_ThemeInterface $theme = null)
    {
        return $this->_objectManager->create(
            'Magento_Core_Model_Layout_File',
            array('filename' => $filename, 'module' => $module, 'theme' => $theme)
        );
    }
}
