<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Magento\Setup\Mvc\Router\Http;

use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\Segment as ZendSegment;

/**
 * Segment route.
 */
class Setup extends ZendSegment
{
    /**
     * @param  Request     $request
     * @param  string|null $pathOffset
     * @param  array       $options
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset = null, array $options = array())
    {
        $routeMatch = parent::match($request, $pathOffset, $options);

        // Add 'Controller' suffix to the controller name
        $controller = $routeMatch->getParam('controller');
        if (false === strpos($controller, 'Controller')) {
            $controller .= 'Controller';
            $routeMatch->setParam('controller', $controller);
        }

        return $routeMatch;
    }
}
