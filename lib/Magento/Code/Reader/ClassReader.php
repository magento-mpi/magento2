<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Code_Reader_ClassReader
{
    /**
     * Read class constructor signature
     *
     * @param string $className
     * @return array|null
     * @throws ReflectionException
     */
    public function getConstructor($className)
    {
        $class = new ReflectionClass($className);
        $result = null;
        $constructor = $class->getConstructor();
        if ($constructor) {
            $result = array();
            /** @var $parameter ReflectionParameter */
            foreach ($constructor->getParameters() as $parameter) {
                $result[] = array(
                    $parameter->getName(),
                    ($parameter->getClass() !== null) ? $parameter->getClass()->getName() : null,
                    !$parameter->isOptional(),
                    $parameter->isOptional() ?
                        $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null :
                        null
                );
            }
        }
        return $result;
    }
}
