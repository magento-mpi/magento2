<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api config model
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Config extends Varien_Simplexml_Config
{
    /**
     * Constructor
     *
     * @see Varien_Simplexml_Config
     */
    public function __construct($sourceData=null)
    {
        parent::__construct($sourceData);
        $this->_construct();
    }

    /**
     * Init configuration for webservices api
     *
     * @return Mage_Api_Model_Config
     */
    protected function _construct()
    {
        $mergeConfig = Mage::getModel('core/config_base');

        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();

        // check if local modules are disabled
        $disableLocalModules = (string)$config->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));

        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if ($disableLocalModules && ('local' === (string)$module->codePool)) {
                    continue;
                }
                $configFile = $config->getModuleDir('etc', $modName).DS.'api.xml';

                if ($mergeConfig->loadFile($configFile)) {
                    $config->extend($mergeConfig, true);
                }
            }
        }

        $this->setXml($config->getNode('api'));
        return $this;
    }
} // Class Mage_Api_Model_Config End