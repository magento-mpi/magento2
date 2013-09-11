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
 * Quick styles configuration
 */
namespace Magento\DesignEditor\Model\Config\Control;

class QuickStyles extends \Magento\DesignEditor\Model\Config\Control\AbstractControl
{
    /**
     * Keys of layout params attributes
     *
     * @var array
     */
    protected $_controlAttributes = array('title', 'tab', 'column');

    /**
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_moduleReader;

    /**
     * @param \Magento\Core\Model\Config\Modules\Reader $moduleReader
     * @param array $configFiles
     */
    public function __construct(\Magento\Core\Model\Config\Modules\Reader $moduleReader, array $configFiles)
    {
        $this->_moduleReader = $moduleReader;
        parent::__construct($configFiles);
    }

    /**
     * Path to quick_styles.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return $this->_moduleReader->getModuleDir('etc', 'Magento_DesignEditor')
            . \Magento\Filesystem::DIRECTORY_SEPARATOR
            . 'quick_styles.xsd';
    }
}
