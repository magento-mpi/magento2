<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub abstract class which provide to change protected property "$_disabledAttrs" and test methods depended on it
 */
namespace Magento\ImportExport\Model\Export;

abstract class StubEntityAbstract
    extends \Magento\ImportExport\Model\Export\EntityAbstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_disabledAttrs = array('default_billing', 'default_shipping');
    }
}
