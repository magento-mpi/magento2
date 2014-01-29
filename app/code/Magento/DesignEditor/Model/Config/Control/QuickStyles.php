<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\Config\Control;

/**
 * Quick styles configuration
 */
class QuickStyles extends \Magento\DesignEditor\Model\Config\Control\AbstractControl
{
    /**
     * Keys of layout params attributes
     *
     * @var string[]
     */
    protected $_controlAttributes = array('title', 'tab', 'column');

    /**
     * @var \Magento\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @param $configFiles
     * @param \Magento\Module\Dir\Reader $moduleReader
     */
    public function __construct(
        $configFiles,
        \Magento\Module\Dir\Reader $moduleReader
    ) {
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
        return $this->_moduleReader->getModuleDir('etc', 'Magento_DesignEditor') . '/quick_styles.xsd';
    }
}
