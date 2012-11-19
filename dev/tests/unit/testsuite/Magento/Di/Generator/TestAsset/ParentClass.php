<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Di\Generator\TestAsset;
use Zend\Code\Generator\DocBlockGenerator;

class ParentClass
{
    /**
     * Public parent method
     *
     * @param \Zend\Code\Generator\DocBlockGenerator $docBlockGenerator
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @param array $array
     */
    public function publicParentMethod(DocBlockGenerator $docBlockGenerator, $param1 = '', $param2 = '\\',
        $param3 = '\'', array $array = array()
    ) {
    }

    /**
     * Protected parent method
     *
     * @param \Zend\Code\Generator\DocBlockGenerator $docBlockGenerator
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @param array $array
     */
    protected function _protectedParentMethod(DocBlockGenerator $docBlockGenerator, $param1 = '', $param2 = '\\',
        $param3 = '\'', array $array = array()
    ) {
    }

    /**
     * Private parent method
     *
     * @param \Zend\Code\Generator\DocBlockGenerator $docBlockGenerator
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @param array $array
     *
     * @PHPMD.SuppressWarnings(UnusedPrivateMethod)
     */
    private function _privateParentMethod(DocBlockGenerator $docBlockGenerator, $param1 = '', $param2 = '\\',
        $param3 = '\'', array $array = array()
    ) {
    }

    public function publicParentWithoutParameters()
    {
    }

    public static function publicParentStatic()
    {
    }

    final public function publicParentFinal()
    {
    }
}
