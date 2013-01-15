<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
    protected function _setListsFromConfig($type)
    {

    }

    protected function _getParentEntity()
    {
    }
}

