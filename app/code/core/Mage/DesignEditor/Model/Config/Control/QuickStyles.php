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
 * Quick styles configuration
 */
class Mage_DesignEditor_Model_Config_Control_QuickStyles extends Mage_DesignEditor_Model_Config_Control_Abstract
{
    /**
     * Keys of layout params attributes
     *
     * @var array
     */
    protected $_controlAttributes = array('title', 'column');

    /**
     * Path to quick_styles.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/../../../etc/quick_styles.xsd';
    }

    /**
     * Getter for initial view.xml contents
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><quick-styles></quick-styles>';
    }

    /**
     * Variables are identified by module and name
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array('/quick-styles/control' => 'name', '/quick-styles/control/components/control' => 'name');
    }
}
