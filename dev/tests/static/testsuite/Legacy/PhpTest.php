<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Legacy_PhpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider phpFileDataProvider
     */
    public function testPhpFile($file)
    {
        $content = file_get_contents($file);
        $this->_testDeprecatedClasses($content);
        $this->_testDeprecatedMethods($content);
        $this->_testDeprecatedMethodArguments($content);
        $this->_testDeprecatedProperties($content);
        $this->_testDeprecatedActions($content);
        $this->_testDeprecatedConstants($content);
    }

    public function phpFileDataProvider()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(PATH_TO_SOURCE_CODE)
        );
        $regexIterator = new RegexIterator($iterator, '/\.(?:php|phtml)$/');
        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $file = (string)$fileInfo;
            if (realpath($file) == __FILE__) {
                continue;
            }
            /* Use filename as a data set name to not include it to every assertion message */
            $result[$file] = array($file);
        }
        return $result;
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedClasses($content)
    {
        // todo: check xml files also
        $deprecations = array(
            'Mage_XmlConnect_Helper_Payment'                                => 'remove it',
            'Mage_Catalog_Model_Entity_Product_Attribute_Frontend_Image'    => 'remove it',
            'Mage_Catalog_Model_Resource_Product_Attribute_Frontend_Image'  => 'remove it',
            'Mage_Bundle_SelectionController'
                => 'use Mage_Bundle_Adminhtml_Bundle_SelectionController instead',
            'Mage_Bundle_Product_EditController'
                => 'use Mage_Bundle_Adminhtml_Bundle_SelectionController instead',
            'Mage_Downloadable_FileController'
                => 'use Mage_Downloadable_Adminhtml_Downloadable_FileController instead',
            'Mage_Downloadable_Product_EditController'
                => 'use Mage_Adminhtml_Catalog_ProductController instead',
            'Mage_GiftMessage_IndexController'
                => 'remove it, gift message is set during checkout process',
            'Mage_GoogleOptimizer_IndexController'
                => 'use Mage_GoogleOptimizer_Adminhtml_Googleoptimizer_IndexController instead',
            'Mage_Shipping_ShippingController'                              => 'remove it',
            'Mage_Page_Block_Html_Toplinks'                                 => 'remove it',
            'Mage_ProductAlert_Block_Price'                                 => 'remove it',
            'Mage_ProductAlert_Block_Stock'                                 => 'remove it',
            'Mage_Sales_Block_Order_Details'                                => 'remove it',
            'Mage_Sales_Block_Order_Tax'                                    => 'remove it',
            'Mage_Tag_Block_Customer_Edit'                                  => 'remove it',
            'Mage_Sales_Model_Entity_Setup'                                 => 'remove it',
            'Enterprise_Search_Model_Resource_Suggestions'                  => 'remove it',
            'Mage_Core_Model_Language'                                      => 'remove it',
            'Mage_Core_Model_Resource_Language'                             => 'remove it',
            'Mage_Core_Model_Resource_Language_Collection'                  => 'remove it'
        );
        foreach ($deprecations as $class => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($class, '/') . '[^a-z\d_]/i',
                $content,
                "Deprecated class '$class' is used, $suggestion."
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedMethods($content)
    {
        $deprecations = array(
            'htmlEscape'                        => 'use escapeHtml() instead',
            'urlEscape'                         => 'use urlEscape() instead',
            'getTrackingPopUpUrlByOrderId'      => 'use getTrackingPopupUrlBySalesModel() instead',
            'getTrackingPopUpUrlByShipId'       => 'use getTrackingPopupUrlBySalesModel() instead',
            'getTrackingPopUpUrlByTrackId'      => 'use getTrackingPopupUrlBySalesModel() instead',
            'isReadablePopupObject'             => 'remove it',
            'getOriginalHeigh'                  => 'use getOriginalHeight() instead',
            'shaCrypt'                          => 'use Mage_Ogone_Model_Api::getHash() instead',
            'shaCryptValidation'                => 'use Mage_Ogone_Model_Api::getHash() instead',
            'getTaxRatesByProductClass'         => 'use _getAllRatesByProductClass() instead',
            'getAddToCartUrlBase64'             => 'use _getAddToCartUrl() instead',
            'isTemplateAllowedForApplication'   => 'remove it',
            '_inludeControllerClass'            => 'use _includeControllerClass() instead',
            '_getSHAInSet'                      => 'use Mage_Ogone_Model_Api::getHash() or $_inShortMap instead',
            '_getAttributeFilterBlockName'      => 'remove it',
            'getIsActiveAanalytics'             => 'use getOnsubmitJs() instead',
            'setTaxGroupFilter'                 => 'remove it',
            'fetchRuleRatesForCustomerTaxClass' => 'remove it',
            'getValueTable'                     => 'remove it, use direct table names'
        );
        foreach ($deprecations as $method => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($method, '/') . '\s*\(/i',
                $content,
                "Deprecated method '$method' is used, $suggestion."
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedMethodArguments($content)
    {
        $deprecations = array(
            'getTypeInstance' => 'remove arguments, refactor code to treat returned type instance as a singleton',
        );
        foreach ($deprecations as $method => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($method, '/') . '\s*\(\s*[^\)]+/i',
                $content,
                "Method '$method' is called with deprecated arguments, $suggestion."
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedProperties($content)
    {
        $deprecations = array(
            'decoratedIsFirst'   => 'use getDecoratedIsFirst() instead',
            'decoratedIsEven'    => 'use getDecoratedIsEven() instead',
            'decoratedIsOdd'     => 'use getDecoratedIsOdd() instead',
            'decoratedIsLast'    => 'use getDecoratedIsLast() instead',
            '_currencyNameTable' => 'remove it'
        );
        foreach ($deprecations as $property => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_]' . preg_quote($property, '/') . '[^a-z\d_]/i',
                $content,
                "Deprecated property '$property' is used, $suggestion."
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedActions($content)
    {
        $deprecations = array(
            'catalog/product/image'
                => 'resizing images upon the client request has been deprecated, use server-side resizing instead',
        );
        foreach ($deprecations as $action => $suggestion) {
            $this->assertNotRegExp(
                '/[^a-z\d_\/]' . preg_quote($action, '/') . '[^a-z\d_\/]/i',
                $content,
                "Deprecated action '$action' is used, $suggestion."
            );
        }
    }

    /**
     * @param string $content
     */
    protected function _testDeprecatedConstants($content)
    {
        $deprecations = array(
            'Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media::GALLERY_IMAGE_TABLE'
                => 'remove it',
            'Mage_Eav_Model_Entity::DEFAULT_VALUE_TABLE_PREFIX' => 'remove it'
        );

        foreach ($deprecations as $constantDeclaration => $suggestion) {
            list($class, $constant) = explode('::', $constantDeclaration);
            // Whether a required class is declared in content
            if (!preg_match('/class\s*' . $class . '(\s*|;)/', $content)) {
                continue;
            }
            // Test that no constant is present
            $this->assertNotRegExp(
                '/const\s*' . $constant . '\s*=/',
                $content,
                "Deprecated constant '$constant' is used, $suggestion."
            );
        }
    }
}
