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
namespace Magento\Catalog\Model\Category\CategoryImageTest;


\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Core\Model\StoreManagerInterface'
)->getStore()->setConfig(
    'dev/log/active',
    1
);
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Core\Model\StoreManagerInterface'
)->getStore()->setConfig(
    'dev/log/exception_file',
    'save_category_without_image.log'
);
class StubZendLogWriterStreamTest extends \Zend_Log_Writer_Stream
{
    /** @var array */
    public static $exceptions = array();

    public function write($event)
    {
        self::$exceptions[] = $event;

        parent::write($event);
    }
}
