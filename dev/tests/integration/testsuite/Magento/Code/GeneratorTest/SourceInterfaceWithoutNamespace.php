<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Code_GeneratorTest_SourceInterfaceWithoutNamespace
    extends Magento_Code_GeneratorTest_ParentInterfaceWithoutNamespace
{
    /**
     * Do some work with params
     *
     * @param $param1
     * @param array $param2
     * @return mixed
     */
    public function doWorkWithParams($param1, array $param2);
}
