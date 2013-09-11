<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

Mage::app()->getStore()->setConfig('dev/log/active', 1);
Mage::app()->getStore()->setConfig('dev/log/exception_file', 'save_category_without_image.log');
$configModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config');
$configModel->setNode(
    'global/log/core/writer_model',
    'Magento_Catalog_Model_Category_CategoryImageTest_StubZendLogWriterStreamTest'
);


class Magento_Catalog_Model_Category_CategoryImageTest_StubZendLogWriterStreamTest extends Zend_Log_Writer_Stream
{
    /** @var array */
    public static $exceptions = array();

    public function write($event)
    {
        self::$exceptions[] = $event;

        parent::write($event);
    }
}
