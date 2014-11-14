<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Di\Compiler;

class ArgumentsResolver
{
    /**
     * @param \Magento\Framework\ObjectManager\Config $config
     * @param $class
     * @param $constructor
     * @return mixed
     */
    static function processConstructor($config, $class, $constructor)
    {
        if (!$constructor) {
            return null;
        }
        $configuredArguments = $config->getArguments($class);
        $configuredArguments = array_map(
            function ($type) {
                if (isset($type['instance'])) {
                    $type['instance'] = ltrim($type['instance'], '\\');
                }

                return $type;
            },
            $configuredArguments
        );

        $arguments = [];
        foreach ($constructor as $parameter) {
            $argument = self::getNonObjectArgument(null);
            list ($paramName, $paramType, $paramRequired, $paramDefault) = $parameter;
            if (isset($configuredArguments[$paramName])) {
                if ($paramType) {
                    if ($config->isShared($configuredArguments[$paramName]['instance'])) {
                        $argument = self::getSharedInstanceArgument($configuredArguments[$paramName]['instance']);
                    } else {
                        $argument = self::getNonSharedInstance(
                            $configuredArguments[$paramName]['instance']
                        );
                    }
                } else {
                    if (isset($configuredArguments[$paramName]['argument'])) {
                        $argument = self::getGlobalArgument(
                            $configuredArguments[$paramName]['argument'],
                            $paramDefault
                        );
                    } else {
                        $argument = self::getNonObjectArgument($configuredArguments[$paramName]);
                    }
                }
            } else {
                if ($paramType) {
                    if (!$paramRequired) {
                        $argument = self::getNonObjectArgument($paramDefault);
                    } else {
                        if ($config->isShared($paramType)) {
                            $argument = self::getSharedInstanceArgument($paramType);
                        } else {
                            $argument = self::getNonSharedInstance($paramType);
                        }
                    }
                } else {
                    if (!$paramRequired) {
                        $argument = self::getNonObjectArgument($paramDefault);
                    }
                }
            }
            $arguments[$paramName] = $argument;
        }
        return $arguments;
    }

    /**
     * Returns argument of non shared instance
     *
     * @param true $instanceType
     * @return array
     */
    static function getNonSharedInstance($instanceType)
    {
        return [
            '__non_shared__' => true,
            '__instance__' => $instanceType
        ];
    }

    /**
     * Returns non object argument
     *
     * @param mixed $value
     * @return array
     */
    static function getNonObjectArgument($value)
    {
        return ['__val__' => $value];
    }

    /**
     * Returns global argument
     *
     * @param string $argument
     * @param string $default
     * @return array
     */
    static function getGlobalArgument($argument, $default)
    {
        return [
            '__arg__' => $argument,
            '__default__' => $default
        ];
    }

    /**
     * Returns shared instance argument
     *
     * @param string $instanceType
     * @return mixed
     */
    static function getSharedInstanceArgument($instanceType)
    {
        return $instanceType;
    }
}
