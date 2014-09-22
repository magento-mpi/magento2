<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Mvc\Router\Http;

use Traversable;
use Zend\Mvc\Router\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\Regex as ZendRegex;
use Zend\Mvc\Router\Http\RouteMatch;

/**
 * Setup route.
 */
class Setup extends ZendRegex
{
    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::factory()
     * @param  array|Traversable $options
     * @return Regex
     * @throws \Zend\Mvc\Router\Exception\InvalidArgumentException
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(
                __METHOD__ . ' expects an array or Traversable set of options'
            );
        }

        if (!isset($options['regex'])) {
            throw new Exception\InvalidArgumentException('Missing "regex" in options array');
        }

        if (strpos($options['regex'], '?<controller>') === false) {
            throw new Exception\InvalidArgumentException('Missing "?<controller>" part in "regex"');
        }

        if (!isset($options['spec'])) {
            throw new Exception\InvalidArgumentException('Missing "spec" in options array');
        }

        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new static($options['regex'], $options['spec'], $options['defaults']);
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @param  Request $request
     * @param  int $pathOffset
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset = null)
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $uri  = $request->getUri();
        $path = $uri->getPath();

        $result = preg_match('(' . $this->regex . ')', $path, $matches, null, (int)$pathOffset);
        if (!$result || !isset($matches['controller'])) {
            return null;
        }

        foreach ($matches as $key => $value) {
            if (is_numeric($key) || is_int($key) || $value === '') {
                unset($matches[$key]);
            }
        }

        $chunks = explode('/', ltrim($path, '/'));
        if ($pathOffset !== null) {
            array_shift($chunks); // Extract 'module name' part
        }
        array_pop($chunks); // Extract 'controller' part

        array_unshift($chunks, $this->defaults['__NAMESPACE__']);
        $namespace = str_replace(' ', '\\', ucwords(implode(' ', $chunks)));

        $controller = ucwords($matches['controller']);
        if (false === strpos($controller, 'Controller')) {
            $controller .= 'Controller';
        }

        $matches['controller'] = $controller;
        $matches['__NAMESPACE__'] = str_replace(' ', '', ucwords(str_replace('-', ' ', $namespace)));

        $matchedLength = strlen($uri->getPath()) - $pathOffset;
        return new RouteMatch(array_merge($this->defaults, $matches), $matchedLength);
    }
}
