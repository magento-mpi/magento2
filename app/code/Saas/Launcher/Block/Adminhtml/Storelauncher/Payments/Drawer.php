<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payments Drawer Block
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer extends Saas_Launcher_Block_Adminhtml_Drawer
{
    /**
     * @var Magento_Backend_Model_Config_Structure
     */
    protected $_configStructure;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Saas_Launcher_Model_LinkTracker $linkTracker
     * @param Magento_Backend_Model_Config_Structure $configStructure
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Saas_Launcher_Model_LinkTracker $linkTracker,
        Magento_Backend_Model_Config_Structure $configStructure,
        array $data = array()
    ) {
        parent::__construct($context, $linkTracker, $data);
        $this->_configStructure = $configStructure;
    }

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Saas_Launcher_Helper_Data')->__('Payments');
    }

    /**
     * @param string $path
     * @return mixed
     * @throws Saas_Launcher_Exception
     */
    public function getMoreUrl($path)
    {
        /** @var Magento_Backend_Model_Config_Structure_ElementInterface $element */
        $element = $this->_configStructure->getElement($path);
        if (!isset($element)) {
            throw new Saas_Launcher_Exception('Element was not found: ' . $path);
        }
        return $element->getAttribute('more_url');
    }
}
