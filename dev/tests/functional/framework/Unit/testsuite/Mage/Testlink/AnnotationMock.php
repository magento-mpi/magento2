<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Testlink_AnnotationMock extends Mage_Testlink_Annotation
{

    /**
     * Just overrides the parent constructor
     */
    public function __construct()
    {
    }

    /**
     * Calls all parent methods, even protected
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $class = new \ReflectionClass('Mage_Testlink_Annotation');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($this, $arguments);
    }
}