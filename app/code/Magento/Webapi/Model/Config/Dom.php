<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Config;

class Dom extends \Magento\Config\Dom
{

    /**
     * Getter for node by path
     *
     * @param string $nodePath
     * @return DOMElement|null
     */
    protected function _getMatchedNode($nodePath)
    {
        if (!preg_match('/^\/config?$/i', $nodePath)) {
            return null;
        }
        return parent::_getMatchedNode($nodePath);
    }
}
