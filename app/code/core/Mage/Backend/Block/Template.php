<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend abstract block
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Template extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    public function __construct(array $data = array())
    {
        parent::__construct($data);
        $this->_objectFactory = isset($data['objectFactory']) ? $data['objectFactory'] : null;
    }

    /**
     * Get object factory model
     *
     * @return Mage_Core_Model_Abstract|Mage_Core_Model_Config
     */
    protected function _getObjectFactory()
    {
        if (null === $this->_objectFactory) {
            $this->_objectFactory = Mage::getSingleton('Mage_Core_Model_Config');
        }

        return $this->_objectFactory;
    }


    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'Mage_Backend_Model_Url';
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('Mage_Core_Model_Session')->getFormKey();
    }

    /**
     * Check whether or not the module output is enabled
     *
     * Because many module blocks belong to Backend module,
     * the feature "Disable module output" doesn't cover Admin area
     *
     * @param string $moduleName Full module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->getModuleName();
        }
        return !Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName);
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_block_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
