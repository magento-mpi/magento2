<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage unit_tests
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Abstract Variable Item Fake
 *
 * Provided for easy unit testing of Saas_PrintedTemplate_Model_Variable_Item_Abstract
 * because of PHPUnit behaves very strange mocking abstract classes
 */
class Saas_PrintedTemplate_Model_Variable_Item_FakeAbstract
    extends Saas_PrintedTemplate_Model_Variable_Item_Abstract
{
    /**
     * @param string $type
     * @return Saas_PrintedTemplate_Model_Variable_Item_FakeAbstract
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _setListsFromConfig($type)
    {
        return $this;
    }

    protected function _getParentEntity()
    {
        return $this;
    }
}

