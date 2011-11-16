<?php
/**
 * Integrity test used to check, that files do not contain the code from removed deprecates
 *
 * {license_notice}
 *
 * @category    tests
 * @package     integration
 * @subpackage  integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_DeprecatesTest extends Magento_Test_TestCase_VisitorAbstract
{
    /**
     * List of classes that are deprecated now (keys) and suggestions on replacements
     * @var array
     */
    protected $_deprecatedClasses = array(
        'Mage_XmlConnect_Helper_Payment' => 'remove this usage',
        'Mage_Catalog_Model_Entity_Product_Attribute_Frontend_Image' => 'remove it, old and non-used model',
        'Mage_Catalog_Model_Resource_Product_Attribute_Frontend_Image' => 'remove it, was not used',
        'Mage_Bundle_SelectionController' => 'use Mage_Bundle_Adminhtml_Bundle_SelectionController',
        'Mage_Bundle_Product_EditController' => 'use Mage_Bundle_Adminhtml_Bundle_SelectionController',
        'Mage_Downloadable_FileController' => 'use Mage_Downloadable_Adminhtml_Downloadable_FileController',
        'Mage_Downloadable_Product_EditController' => 'use Mage_Adminhtml_Catalog_ProductController',
        'Mage_GiftMessage_IndexController' => 'remove it, gift message is set during checkout process',
        'Mage_GoogleOptimizer_IndexController' => 'use Mage_GoogleOptimizer_Adminhtml_Googleoptimizer_IndexController',
        'Mage_Shipping_ShippingController' => 'remove it, not used anymore'
    );

    /**
     * List of deprecated methods. Only unique names must be placed there, so we don't get wrong alerts.
     */
    protected $_deprecatedMethods = array(
        array('method' => 'isReadablePopupObject', 'suggestion' => 'remove it'),
        array('method' => 'getOriginalHeigh', 'suggestion' => 'use getOriginalHeigh()'),
        array('method' => 'shaCrypt', 'suggestion' => 'use Mage_Ogone_Model_Api::getHash()'),
        array('method' => 'shaCryptValidation', 'suggestion' => 'use Mage_Ogone_Model_Api::getHash()'),
        array('method' => 'getTaxRatesByProductClass', 'suggestion' => 'use _getAllRatesByProductClass()'),
        array('method' => 'getAddToCartUrlBase64', 'suggestion' => 'use _getAddToCartUrl()'),
        array('method' => 'isTemplateAllowedForApplication', 'suggestion' => 'remove it'),
        array('method' => '_inludeControllerClass', 'suggestion' => 'use _includeControllerClass()'),
        array('method' => '_getSHAInSet', 'suggestion' => 'use Mage_Ogone_Model_Api - getHash() or $_inShortMap')
    );

    /**
     * @return void
     */
    public function testFindDeprecatedStuff()
    {
        $found = $this->_findDeprecates();
        $this->assertEmpty($found, implode(".\n", $found));
    }

    /**
     * Gathers all deprecated or removed stuff
     *
     * @return array
     */
    protected function _findDeprecates()
    {
        $directory  = new RecursiveDirectoryIterator(Mage::getRoot());
        $iterator = new RecursiveIteratorIterator($directory);
        $regexIterator = new RegexIterator($iterator, '/(\.php|\.phtml|\.xml|\.js)$/');

        $result = array();
        foreach ($regexIterator as $fileInfo) {
            $deprecates = $this->_findDeprecatesInFile($fileInfo);
            $result = array_merge($result, $deprecates);
        }

        return $result;
    }

    /**
     * Gathers all deprecated or removed stuff in a file
     *
     * @param SplFileInfo $fileInfo
     * @return array
     */
    protected function _findDeprecatesInFile($fileInfo)
    {
        $content = file_get_contents((string) $fileInfo);

        $result = array();
        $visitorMethods = $this->_getVisitorMethods();
        foreach ($visitorMethods as $method) {
            $deprecates = $this->$method($fileInfo, $content);
            $result = array_merge($result, $deprecates);
        }

        if (!$result) {
            return $result;
        }

        $filePath = substr($fileInfo, strlen(Mage::getRoot()) - 3); // 3 - length of 'app' prefix
        foreach ($result as $key => $value) {
            $result[$key] = 'Found deprecated and removed ' . $value['description'] . ' "' . $value['needle'] . '" in '
                . $filePath . ', suggested: ' . $value['suggestion'];
        }

        return $result;
    }

    /**
     * Finds usage of htmlEscape method
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitHtmlEscape($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $result = array();
        if (strpos($content, 'htmlEscape(') !== false) {
            $result[] = array(
                'description' => 'method',
                'needle' => 'htmlEscape()',
                'suggestion' => 'change to escapeHtml()'
            );
        }

        return $result;
    }

    /**
     * Finds usage of urlEscape method
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitUrlEscape($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $result = array();
        if (strpos($content, 'urlEscape(') !== false) {
            $result[] = array(
                'description' => 'method',
                'needle' => 'urlEscape()',
                'suggestion' => 'change to escapeUrl()'
            );
        }

        return $result;
    }

    /**
     * Finds usage of deprecated methods that compose shipping tracking urls
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitTrackingPopUpUrl($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $search = array(
            'getTrackingPopUpUrlByOrderId(',
            'getTrackingPopUpUrlByShipId(',
            'getTrackingPopUpUrlByTrackId('
        );

        $result = array();
        foreach ($search as $needle) {
            if (strpos($content, $needle) === false) {
                continue;
            }
            $result[] = array(
                'description' => 'method',
                'needle' => $needle . ')',
                'suggestion' => 'use getTrackingPopupUrlBySalesModel()'
            );
        }

        return $result;
    }

    /**
     * Finds usage of deprecated methods that duplicates invitation config model
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitInvitationHelperMethods($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        // Optimization to not use regexps where not needed
        if (strpos($content, 'Enterprise_Invitation_Helper_Data') === false) {
            return array();
        }

        $methods = array(
            'getMaxInvitationsPerSend',
            'getInvitationRequired',
            'getUseInviterGroup',
            'isInvitationMessageAllowed',
            'isEnabled'
        );

        $result = array();
        foreach ($methods as $method) {
            $pattern = '/Enterprise_Invitation_Helper_Data[^;(]+' . $method . '\(/';
            if (!preg_match($pattern, $content)) {
                continue;
            }
            $result[] = array(
                'description' => 'invitation helper method',
                'needle' => $method . '()',
                'suggestion' => "use Mage::getSingleton('Enterprise_Invitation_Model_Config')->{$method}()"
            );
        }

        return $result;
    }

    /**
     * Finds usage of deprecated classes
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitDeprecatedClasses($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml', 'xml'))) {
            return array();
        }

        $result = array();
        foreach ($this->_deprecatedClasses as $class => $suggestion) {
            $pos = strpos($content, $class);
            if ($pos === false) {
                continue;
            }

            // Check we didn't find a substring of other class, i.e. class name ends with separator
            $nextSymbol = substr($content, $pos + strlen($class), 1);
            if (preg_match('/[[:alnum:]_]/', $nextSymbol)) {
                continue;
            }

            $result[] = array(
                'description' => 'class',
                'needle' => $class,
                'suggestion' => $suggestion
            );
        }

        return $result;
    }

    /**
     * Finds usage of deprecated methods
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitDeprecatedMethods($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        $result = array();
        foreach ($this->_deprecatedMethods as $searchInfo) {
            $method = $searchInfo['method'];
            $suggestion = $searchInfo['suggestion'];
            $needle = $method . '(';
            if (strpos($content, $needle) === false) {
                continue;
            }
            $result[] = array(
                'description' => 'method',
                'needle' => $method . '()',
                'suggestion' => $suggestion
            );
        }
        return $result;
    }

    /**
     * Finds usage of deprecated image resizing action
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitCatalogImageResizingAction($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml'))) {
            return array();
        }

        if (strpos($content, 'catalog/product/image') === false) {
            return array();
        }

        return array(
            array(
                'description' => 'controller action',
                'needle' => 'catalog/product/image',
                'suggestion' => 'resize image at server size, do not resize it on client request'
            )
        );
    }

    /**
     * Finds usage of deprecated property skipCalculate
     *
     * @param SplFileInfo $fileInfo
     * @param string $content
     * @return array
     */
    protected function _visitSkipCalculate($fileInfo, $content)
    {
        if (!$this->_fileHasExtensions($fileInfo, array('php', 'phtml', 'js'))) {
            return array();
        }

        if ($this->_fileHasExtensions($fileInfo, 'js')) {
            $needle = '.skipCalculate';
        } else {
            $needle = "'skipCalculate'";
        }

        $result = array();
        if (strpos($content, $needle) !== false) {
            $result = array(
                array(
                    'description' => 'deprecated configuration property',
                    'needle' => 'skipCalculate',
                    'suggestion' => 'remove it'
                )
            );
        }
        return $result;
    }
}
