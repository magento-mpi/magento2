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
 * Theme css file model class
 */
class Mage_Core_Model_Theme_Customization_Files_Css extends Mage_Core_Model_Theme_Customization_Files_FilesAbstract
{
    /**
     * Custom css type
     */
    const CUSTOM_CSS = 'custom';

    /**
     * Quick style css type
     */
    const QUICK_STYLE_CSS = 'quick_style';

    /**
     * Css file type customization
     */
    const TYPE = 'css_file';

    /**
     * Css files by type
     *
     * @var array
     */
    protected $_cssFiles = array(
        self::CUSTOM_CSS      => 'css/custom.css',
        self::QUICK_STYLE_CSS => 'css/quick_style.css'
    );

    /**
     * Return css file customization type
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * Return file type
     *
     * @return string
     */
    protected function _getFileType()
    {
        return Mage_Core_Model_Theme_Files::TYPE_CSS;
    }

    /**
     * Get CSS file name by type
     *
     * @param string $type
     * @return string
     * @throws InvalidArgumentException
     */
    protected function _getFileName($type)
    {
        if (!array_key_exists($type, $this->_cssFiles)) {
            throw new InvalidArgumentException('Invalid CSS file type');
        }
        return $this->_cssFiles[$type];
    }

    /**
     * Save data
     *
     * @param $theme Mage_Core_Model_Theme
     * @return Mage_Core_Model_Theme_Customization_Files_Css
     */
    protected function _save($theme)
    {
        foreach ($this->_dataForSave as $type => $cssFileContent) {
            /** @var $cssFiles Mage_Core_Model_Theme_Files */
            $cssFile = $this->getCollectionByTheme($theme, $type)->getFirstItem();

            $cssFile->addData(array(
                'theme_id'  => $theme->getId(),
                'file_path' => $this->_getFileName($type),
                'file_type' => $this->_getFileType(),
                'content'   => $cssFileContent
            ))->save();
        }

        return $this;
    }

    /**
     * Get theme collection
     *
     * @param Mage_Core_Model_Theme_Customization_CustomizedInterface $theme
     * @param null|string $type
     * @return Mage_Core_Model_Resource_Theme_Files_Collection
     */
    public function getCollectionByTheme(
        Mage_Core_Model_Theme_Customization_CustomizedInterface $theme, $type = null
    ) {
        return (null === $type)
            ? parent::getCollectionByTheme($theme)
            : parent::getCollectionByTheme($theme)->addFilter('file_path', $this->_getFileName($type));
    }
}
