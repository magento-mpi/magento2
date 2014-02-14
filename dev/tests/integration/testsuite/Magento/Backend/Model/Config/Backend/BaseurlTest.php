<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Backend;

/**
 * @magentoAppArea adminhtml
 */
class BaseurlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $path
     * @param string $value
     * @magentoDbIsolation enabled
     * @dataProvider validationDataProvider
     */
    public function testValidation($path, $value)
    {
        /** @var $model \Magento\Backend\Model\Config\Backend\Baseurl */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Config\Backend\Baseurl');
        $model->setPath($path)->setValue($value)->save();
        $this->assertNotEmpty((int)$model->getId());
    }

    /**
     * @return array
     */
    public function validationDataProvider()
    {
        $basePlaceholder = '{{base_url}}';
        $unsecurePlaceholder = '{{unsecure_base_url}}';
        $unsecureSuffix = '{{unsecure_base_url}}test/';
        $securePlaceholder = '{{secure_base_url}}';
        $secureSuffix = '{{secure_base_url}}test/';

        return array(
            // any fully qualified URLs regardless of path
            array('any/path', 'http://example.com/'),
            array('any/path', 'http://example.com/uri/'),

            // unsecure base URLs
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_URL, $basePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_LINK_URL, $unsecurePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_LINK_URL, $unsecureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_STATIC_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_STATIC_URL, $unsecurePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_STATIC_URL, $unsecureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_CACHE_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_CACHE_URL, $unsecurePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_CACHE_URL, $unsecureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_MEDIA_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_MEDIA_URL, $unsecurePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_MEDIA_URL, $unsecureSuffix),

            // secure base URLs
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_URL, $basePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LINK_URL, $securePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LINK_URL, $secureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_STATIC_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_STATIC_URL, $securePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_STATIC_URL, $secureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_CACHE_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_CACHE_URL, $securePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_CACHE_URL, $secureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_MEDIA_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_MEDIA_URL, $securePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_MEDIA_URL, $secureSuffix),

            // secure base URLs - in addition can use unsecure
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_URL, $unsecurePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LINK_URL, $unsecurePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LINK_URL, $unsecureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_STATIC_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_STATIC_URL, $unsecurePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_STATIC_URL, $unsecureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_CACHE_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_CACHE_URL, $unsecurePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_CACHE_URL, $unsecureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_MEDIA_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_MEDIA_URL, $unsecurePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_MEDIA_URL, $unsecureSuffix),
        );
    }

    /**
     * @param string $path
     * @param string $value
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Core\Exception
     * @dataProvider validationExceptionDataProvider
     */
    public function testValidationException($path, $value)
    {
        /** @var $model \Magento\Backend\Model\Config\Backend\Baseurl */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Config\Backend\Baseurl');
        $model->setPath($path)->setValue($value)->save();
    }

    /**
     * @return array
     */
    public function validationExceptionDataProvider()
    {
        $baseSuffix = '{{base_url}}test/';
        $unsecurePlaceholder = '{{unsecure_base_url}}';
        $unsecureSuffix = '{{unsecure_base_url}}test/';
        $unsecureWrongSuffix = '{{unsecure_base_url}}test';
        $securePlaceholder = '{{secure_base_url}}';
        $secureSuffix = '{{secure_base_url}}test/';
        $secureWrongSuffix = '{{secure_base_url}}test';

        return array(
            // not a fully qualified URLs regardless path
            array('', 'not a valid URL'),
            array('', 'example.com'),
            array('', 'http://example.com'),
            array('', 'http://example.com/uri'),

            // unsecure base URLs
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_URL, ''), // breaks cache
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_URL, $baseSuffix), // creates redirect loops
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_URL, $unsecureSuffix),
            array(
                \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_URL,
                $unsecurePlaceholder
            ), // creates endless recursion
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_LINK_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_LINK_URL, $baseSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_LINK_URL, $unsecureWrongSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_MEDIA_URL, $unsecureWrongSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_STATIC_URL, $unsecureWrongSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_CACHE_URL, $unsecureWrongSuffix),

            // secure base URLs
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_URL, $baseSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_URL, $secureSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_URL, $securePlaceholder),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LINK_URL, ''),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LINK_URL, $baseSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_LINK_URL, $secureWrongSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_MEDIA_URL, $secureWrongSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_STATIC_URL, $secureWrongSuffix),
            array(\Magento\Core\Model\Store::XML_PATH_SECURE_BASE_CACHE_URL, $secureWrongSuffix),
        );
    }
}
