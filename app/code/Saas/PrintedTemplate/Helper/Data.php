<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Printed templates data helper
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Helpers
 */
class Saas_PrintedTemplate_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Returns helper
     *
     * @return Magento_Core_Helper_Abstract
     */
    protected function _getBackendHelper()
    {
        return Mage::helper('Magento_Backend_Helper_Data');
    }

    /**
     * Returns menu config model
     *
     * @return Magento_Backend_Model_Menu_Config
     */
    protected function _getMenuConfig()
    {
        return Mage::getSingleton('Magento_Backend_Model_Menu_Config');
    }

    /**
     * Returns config structure model
     *
     * @return Magento_Backend_Model_Config_Structure
     */
    protected function _getConfigStructure()
    {
        return Mage::getSingleton('Magento_Backend_Model_Config_Structure');
    }

    /**
     * Proxy to Mage::registry();
     *
     * @param string $typeId
     * @return string|null Value
     */
    protected function _registry($typeId)
    {
        return Mage::registry($typeId);
    }

    /**
     * Get onclick script for Print button on Invoice View page
     *
     * @param string $type
     * @return string
     */
    public function getPrintButtonOnclick($type)
    {
        return "setLocation('{$this->_getPrintUrl($type)}')";
    }

    /**
     * Get print URL for specified entity type
     *
     * @param string $type
     * @return string
     */
    protected function _getPrintUrl($type)
    {
        $model = $this->_registry('current_' . $type);
        if (!$model || !$model->getId()) {
            return '';
        }

        return $this->_getBackendHelper()->getUrl('adminhtml/print/entity/', array(
            'type' => $type,
            'id'   => $model->getId(),
        ));
    }

    /**
     * Convert xml config paths to decorated names
     * Method is used to show path to where is config template is used.
     *
     * @param array $paths
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getSystemConfigPathsParts($paths)
    {
        $result = $urlParams = $prefixParts = array();
        $scopeLabel = $this->_getBackendHelper()->__('GLOBAL');
        if ($paths) {
            /**
             * @todo check functionality of getting Magento_Backend_Model_Menu_Config object
             */
            /** @var $menu Magento_Backend_Model_Menu */
            $menu = $this->_getMenuConfig()->getMenu();
            $item = $menu->get('Magento_Adminhtml::system');
            // create prefix path parts
            $prefixParts[] = array(
                'title' => $item->getModuleHelper()->__($item->getTitle()),
            );
            $item = $menu->get('Magento_Adminhtml::system_config');
            $prefixParts[] = array(
                'title' => $item->getModuleHelper()->__($item->getTitle()),
                'url' => $this->_getBackendHelper()->getUrl('adminhtml/system_config/'),
            );

            $pathParts = $prefixParts;
            foreach ($paths as $pathData) {
                $pathDataParts = explode('/', $pathData['path']);
                $sectionName = array_shift($pathDataParts);

                $urlParams = array('section' => $sectionName);
                if (isset($pathData['scope']) && isset($pathData['scope_id'])) {
                    switch ($pathData['scope']) {
                        case 'stores':
                            $store = Mage::app()->getStore($pathData['scope_id']);
                            if ($store) {
                                $urlParams['website'] = $store->getWebsite()->getCode();
                                $urlParams['store'] = $store->getCode();
                                $scopeLabel = $store->getWebsite()->getName() . '/' . $store->getName();
                            }
                            break;
                        case 'websites':
                            $website = Mage::app()->getWebsite($pathData['scope_id']);
                            if ($website) {
                                $urlParams['website'] = $website->getCode();
                                $scopeLabel = $website->getName();
                            }
                            break;
                        default:
                            break;
                    }
                }
                /**
                 * @todo check functionality of getting Magento_Backend_Model_Config_Structure object
                 */
                $pathParts[] = array(
                    'title' => $this->_getConfigStructure()->getElement($sectionName)->getLabel(),
                    'url' => $this->_getBackendHelper()->getUrl('adminhtml/system_config/edit', $urlParams),
                );
                $elementPathParts = array($sectionName);
                while (count($pathDataParts) != 1) {
                    $elementPathParts[] = array_shift($pathDataParts);
                    $pathParts[] = array(
                        'title' => $this->_getConfigStructure()
                            ->getElementByPathParts($elementPathParts)
                            ->getLabel(),
                    );
                }
                $elementPathParts[] = array_shift($pathDataParts);
                $pathParts[] = array(
                    'title' => $this->_getConfigStructure()
                        ->getElementByPathParts($elementPathParts)
                        ->getLabel(),
                    'scope' => $scopeLabel,
                );
                $result[] = $pathParts;
                $pathParts = $prefixParts;
            }
        }

        return $result;
    }
}
