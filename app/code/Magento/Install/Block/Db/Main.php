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
 * Database config installation block
 */
class Magento_Install_Block_Db_Main extends Magento_Core_Block_Template
{
    /**
     * Array of Database blocks keyed by name
     *
     * @var array
     */
    protected $_databases = array();

    /**
     * Install installer config
     *
     * @var Magento_Install_Model_Installer_Config
     */
    protected $_installerConfig = null;

    /**
     * Install installer config
     *
     * @var Magento_Core_Model_Session_Generic
     */
    protected $_session;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Install_Model_Installer_Config $installerConfig
     * @param Magento_Core_Model_Session_Generic $session
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Install_Model_Installer_Config $installerConfig,
        Magento_Core_Model_Session_Generic $session,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_installerConfig = $installerConfig;
        $this->_session = $session;
    }

    /**
     * Adding customized database block template for database model type
     *
     * @param  string $type database type
     * @param  string $block database block type
     * @param  string $template
     * @return Magento_Install_Block_Db_Main
     */
    public function addDatabaseBlock($type, $block, $template)
    {
        $this->_databases[$type] = array(
            'block'     => $block,
            'template'  => $template,
            'instance'  => null
        );

        return $this;
    }

    /**
     * Retrieve database block by type
     *
     * @param  string $type database model type
     * @return bool|Magento_Core_Block_Template
     */
    public function getDatabaseBlock($type)
    {
        $block = false;
        if (isset($this->_databases[$type])) {
            if ($this->_databases[$type]['instance']) {
                $block = $this->_databases[$type]['instance'];
            } else {
                $block = $this->getLayout()->createBlock($this->_databases[$type]['block'])
                    ->setTemplate($this->_databases[$type]['template'])
                    ->setIdPrefix($type);
                $this->_databases[$type]['instance'] = $block;
            }
        }
        return $block;
    }

    /**
     * Retrieve database blocks
     *
     * @return array
     */
    public function getDatabaseBlocks()
    {
        $databases = array();
        foreach (array_keys($this->_databases) as $type) {
            $databases[] = $this->getDatabaseBlock($type);
        }
        return $databases;
    }

    /**
     * Retrieve configuration form data object
     *
     * @return Magento_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = $this->_session->getConfigData(true);
            if (empty($data)) {
                $data = $this->_installerConfig->getFormData();
            } else {
                $data = new Magento_Object($data);
            }
            $this->setFormData($data);
        }
        return $data;
    }

}
