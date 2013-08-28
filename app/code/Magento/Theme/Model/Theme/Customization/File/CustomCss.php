<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Theme customization service class for custom css
 */
class Magento_Theme_Model_Theme_Customization_File_CustomCss
    extends Magento_Core_Model_Theme_Customization_FileAbstract
{
    /**#@+
     * Custom CSS file type customization
     */
    const TYPE = 'custom_css';
    const CONTENT_TYPE = 'css';
    /**#@-*/

    /**
     * Default filename
     */
    const FILE_NAME = 'custom.css';

    /**
     * Default order position
     */
    const SORT_ORDER = 10;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return self::CONTENT_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    protected  function _prepareFileName(Magento_Core_Model_Theme_FileInterface $file)
    {
        if (!$file->getFileName()) {
            $file->setFileName(self::FILE_NAME);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareSortOrder(Magento_Core_Model_Theme_FileInterface $file)
    {
        $file->setData('sort_order', self::SORT_ORDER);
    }
}
