<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base Test Case
 */
abstract class Mage_PHPUnit_TestCase extends PHPUnit_Framework_TestCase
{

    /**
     * Selenium Test Configuration instance
     *
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_config = null;

    /**
     * Class constructor
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_config = Mage_Selenium_TestConfiguration::initInstance();
    }

    /**
     * Retrieve Page from uimap data configuration by path
     *
     * @param string $area Application area ('frontend'|'admin')
     * @param string $pageKey UIMap page key
     * @return Mage_Selenium_Uimap_Page|Null
     */
    public function getUimapPage($area, $pageKey)
    {
        $uimapHelper = $this->_config->getUimapHelper();
        if($uimapHelper) return $uimapHelper->getUimapPage($area, $pageKey);

        return null;
    }

}