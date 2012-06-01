<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Config
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_appConfig;

    /**
     * Menu model
     *
     * @var Mage_Backend_Model_Menu
     */
    protected $_menu;

    public function __construct(array $arguments = array())
    {
        $this->_appConfig = isset($arguments['appConfig']) ? $arguments['appConfig'] : Mage::getConfig();
    }

    /**
     * Build menu model from config
     *
     * @return Mage_Backend_Model_Menu
     */
    public function getMenu()
    {
        if (!$this->_menu) {
            $director = $this->_appConfig->getModelInstance(
                'Mage_Backend_Model_Menu_Director_Dom',
                array(
                    'config' => $this->_getDom(),
                    'factory' => $this->_appConfig
                )
            );
            $itemFactory = $this->_appConfig->getModelInstance(
                'Mage_Backend_Model_Menu_Item_Factory',
                array(
                    'acl' => Mage::getSingleton('Mage_Backend_Model_Auth_Session'),
                    'objectFactory' => $this->_appConfig,
                    'urlModel' => Mage::getSingleton('Mage_Backend_Model_Url')
                )
            );
            $menu = new Mage_Backend_Model_Menu();
            $builder = $this->_appConfig->getModelInstance(
                'Mage_Backend_Model_Menu_Builder',
                array(
                    'menu' => $menu,
                    'itemFactory' => $itemFactory,
                )
            );
            $director->buildMenu($builder);
            $this->_menu = $builder->getResult();
        }
        return $this->_menu;
    }

    /**
     * @return DOMDocument
     */
    protected function _getDom()
    {
        $fileList = $this->getMenuConfigurationFiles();
        return $this->_appConfig->getModelInstance('Mage_Backend_Model_Menu_Config_Menu', $fileList)->getMergedConfig();
    }

    /**
     * Return array menu configuration files
     *
     * @return array
     */
    public function getMenuConfigurationFiles()
    {
        $files = $this->_appConfig
            ->getModuleConfigurationFiles('adminhtml' . DIRECTORY_SEPARATOR . 'menu.xml');
        return (array) $files;
    }
}
