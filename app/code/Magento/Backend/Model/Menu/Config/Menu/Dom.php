<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Menu\Config\Menu;

/**
 * Menu configuration files handler
 */
class Dom extends \Magento\Config\Dom
{
    /**
     * Getter for node by path
     *
     * @param string $nodePath
     * @return \DOMElement|null
     * @throws \Magento\Exception an exception is possible if original document contains multiple fixed nodes
     */
    protected function _getMatchedNode($nodePath)
    {
        if (!preg_match('/^\/config(\/menu)?$/i', $nodePath)) {
            return null;
        }
        return parent::_getMatchedNode($nodePath);
    }
}
