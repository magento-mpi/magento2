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
    'Magento\Framework\App\Config\MutableScopeConfigInterface'
)->setValue(
    'dev/log/active',
    1,
    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
);

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\App\Config\MutableScopeConfigInterface'
)->setValue(
    'dev/log/exception_file',
    'save_category_without_image.log',
    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
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
