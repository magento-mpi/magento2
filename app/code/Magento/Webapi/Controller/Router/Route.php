<?php
/**
 * Abstract route for Magento web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Router;

class Route extends \Zend_Controller_Router_Route
{
    /**
     * Matches a Request with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param \Magento\Webapi\Controller\Request $request
     * @param boolean $partial Partial path matching
     * @return array|bool An array of assigned values or a boolean false on a mismatch
     */
    public function match($request, $partial = false)
    {
        return parent::match(ltrim($request->getPathInfo(), $this->_urlDelimiter), $partial);
    }
}
