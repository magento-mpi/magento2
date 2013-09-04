<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Menu configuration files handler
 */
class Magento_Backend_Model_Menu_Config_Menu_Dom extends \Magento\Config\Dom
{

    /**
     * Getter for node by path
     *
     * @param string $nodePath
     * @throws \Magento\Exception an exception is possible if original document contains multiple fixed nodes
     * @return DOMElement | null
     */
    protected function _getMatchedNode($nodePath)
    {
        if (!preg_match('/^\/config(\/menu)?$/i', $nodePath)) {
            return null;
        }
        return parent::_getMatchedNode($nodePath);
    }
}
