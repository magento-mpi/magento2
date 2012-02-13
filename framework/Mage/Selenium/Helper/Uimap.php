<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * UIMap helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Uimap extends Mage_Selenium_Helper_Abstract
{
    /**
     * @var array
     */
    protected $_configFixtures = array();
    /**
     * @var array
     */
    protected $_fallbackOrderFixture = array();
    /**
     * Uimap data
     * @var array
     */
    protected $_uimapData = array();

    /**
     * Initialize process
     */
    protected function _init()
    {
        $this->_initFixturePath();
        $config = $frameworkConfig = $this->getConfig()->getHelper('config')->getConfigFramework();
        if ($config['load_all_fixtures']) {
            $this->_loadUimapData();
        }
    }

    /**
     * Get all paths to *.yml files
     */
    protected function _initFixturePath()
    {
        $configHelper = $this->getConfig()->getHelper('config');
        $applicationConfig = $configHelper->getApplicationConfig();
        $frameworkConfig = $configHelper->getConfigFramework();
        //get projects sequence
        $this->_fallbackOrderFixture = array_reverse(array_map('trim', explode(',',
                                                                               $applicationConfig['fallbackOrderFixture'])));
        $initialPath = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . $frameworkConfig['fixture_base_path'];
        foreach ($this->_fallbackOrderFixture as $codePoolName) {
            $projectPath = $initialPath . DIRECTORY_SEPARATOR . $codePoolName;
            if (!is_dir($projectPath)) {
                continue;
            }
            $this->_getFilesPath($this->_scanDirectory($projectPath), '', $codePoolName);
        }
    }

    /**
     * Get path to files in directory
     *
     * @param array $pathData
     * @param string $path
     * @param string $project
     *
     * @return mixed
     */
    protected function _getFilesPath($pathData, $path = '', $project)
    {
        $currentPath = preg_replace('|' . preg_quote(DIRECTORY_SEPARATOR) . '(\w)+$' . '|', '', $path);
        $separator = preg_quote(DIRECTORY_SEPARATOR);
        foreach ($pathData as $key => $value) {
            if (is_array($value)) {
                $path .= DIRECTORY_SEPARATOR . $key;
                $path = $this->_getFilesPath($value, $path, $project);
            } else {
                $path = preg_replace('|' . preg_quote(DIRECTORY_SEPARATOR) . '(\w)+\.yml$|', '', $path);
                $path .= DIRECTORY_SEPARATOR . $value;
                if (preg_match('|' . $separator . 'data' . $separator . '|', $path)) {
                    $this->_configFixtures[$project]['data'][] = $path;
                }
                if (preg_match('|' . $separator . 'uimap' . $separator . '|', $path)) {
                    $this->_configFixtures[$project]['uimap'][] = $path;
                }
            }

        }
        return $currentPath;
    }

    /**
     * Get all folder and file names in directory
     * @param string $path
     *
     * @return array
     */
    protected function _scanDirectory($path)
    {
        $directories = array();
        $folderIndicator = opendir($path);
        while ($value = readdir($folderIndicator))
        {
            if ($value == '.' or $value == '..') {
                continue;
            }
            if (!is_dir($path . DIRECTORY_SEPARATOR . $value)) {
                $directories[] = $value;
            }
            if (is_dir($path . DIRECTORY_SEPARATOR . $value)) {
                $directories[$value] = $this->_scanDirectory($path . DIRECTORY_SEPARATOR . $value);
            }
        }

        return $directories;
    }

    /**
     * Get fixture paths
     * @return array
     */
    public function getConfigFixtures()
    {
        return $this->_configFixtures;
    }

    /**
     * Load and merge data files
     * @return Mage_Selenium_Helper_Uimap
     */
    protected function _loadUimapData()
    {
        if ($this->_uimapData) {
            return $this;
        }

        $configHelper = $this->getConfig()->getHelper('config');
        $configAreas = $configHelper->getConfigAreas();
        $frameworkConfig = $configHelper->getConfigFramework();

        $initialPath = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . $frameworkConfig['fixture_base_path'];
        $separator = preg_quote(DIRECTORY_SEPARATOR);

        foreach ($this->_configFixtures as $codePoolName => $codePoolData) {
            $projectPath = $initialPath . DIRECTORY_SEPARATOR . $codePoolName;
            foreach ($configAreas as $areaKey => $areaConfig) {
                $pages = array();
                foreach ($codePoolData['uimap'] as $file) {
                    $pattern = implode($separator, array('', 'uimap', $areaKey, ''));
                    if (preg_match('|' . $pattern . '|', $file)) {
                        $pages = array_merge($this->getConfig()->getHelper('file')->loadYamlFile($projectPath . $file),
                                             $pages);
                    }
                }
                foreach ($pages as $pageKey => $content) {
                    if ($content) {
                        $this->_uimapData[$areaKey][$pageKey] = new Mage_Selenium_Uimap_Page($pageKey, $content);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Retrieve array with UIMap data
     *
     * @param string $area Application area
     *
     * @return mixed
     * @throws OutOfRangeException
     */
    public function getAreaUimaps($area)
    {
        if (!array_key_exists($area, $this->_uimapData)) {
            throw new OutOfRangeException('UIMaps for "' . $area . '" area do not exist');
        }

        return $this->_uimapData[$area];
    }

    /**
     * Retrieve Page from UIMap data configuration by path
     *
     * @param string $area Application area
     * @param string $pageKey UIMap page key
     * @param null|Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return mixed
     * @throws OutOfRangeException
     */
    public function getUimapPage($area, $pageKey, $paramsDecorator = null)
    {
        $areaUimaps = $this->getAreaUimaps($area);
        if (!array_key_exists($pageKey, $areaUimaps)) {
            throw new OutOfRangeException('Can not find page "' . $pageKey . '" in area "' . $area . '"');
        }
        $page = $areaUimaps[$pageKey];
        if ($paramsDecorator) {
            $page->assignParams($paramsDecorator);
        }
        return $page;
    }

    /**
     * Retrieve Page from UIMap data configuration by MCA
     *
     * @param string $area Application area
     * @param string $mca
     * @param null|Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return mixed
     * @throws OutOfRangeException
     */
    public function getUimapPageByMca($area, $mca, $paramsDecorator = null)
    {
        $mca = trim($mca, ' /\\');
        $isExpectedPage = false;
        foreach ($this->_uimapData[$area] as &$page) {
            // get mca without any modifications
            $pageMca = trim($page->getMca(new Mage_Selenium_Helper_Params()), ' /\\');
            if ($pageMca !== false && $pageMca !== null) {
                if ($paramsDecorator) {
                    $pageMca = $paramsDecorator->replaceParametersWithRegexp($pageMca);
                }
                if ($area == 'admin' || $area == 'frontend') {
                    if (preg_match(';^' . $pageMca . '$;', $mca)) {
                        $isExpectedPage = true;
                    }
                } elseif ($this->_compareMcaAndPageMca($mca, $pageMca)) {
                    $isExpectedPage = true;
                }
                if ($isExpectedPage) {
                    if ($paramsDecorator) {
                        $page->assignParams($paramsDecorator);
                    }

                    return $page;
                }
            }
        }
        throw new OutOfRangeException('Can not find page with mca "' . $mca . '" in "' . $area . '" area');
    }

    /**
     * Compares mca from current url and from area mca array
     *
     * @param string $mca
     * @param string $page_mca
     *
     * @return bool
     */
    protected function _compareMcaAndPageMca($mca, $page_mca)
    {
        if (parse_url($page_mca, PHP_URL_PATH) == parse_url($mca, PHP_URL_HOST) . parse_url($mca, PHP_URL_PATH)) {
            parse_str(parse_url($mca, PHP_URL_QUERY), $mca_params);
            parse_str(parse_url($page_mca, PHP_URL_QUERY), $page_mca_params);
            if (array_keys($mca_params) == array_keys($page_mca_params)) {
                foreach ($page_mca_params as $key => $value) {
                    if ($mca_params[$key] != $value && $value != '%anyValue%') {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Return URL of a specified page
     *
     * @param string $area Application area
     * @param string $page UIMap page key
     * @param null|Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return string
     */
    public function getPageUrl($area, $page, $paramsDecorator = null)
    {
        $baseUrl = $this->getConfig()->getHelper('config')->getBaseUrl();
        return $baseUrl . $this->getPageMca($area, $page, $paramsDecorator);
    }

    /**
     * Return Page Mca
     *
     * @param string $area Application area
     * @param string $page UIMap page key
     * @param null|Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return mixed
     */
    public function getPageMca($area, $page, $paramsDecorator = null)
    {
        $pageUimap = $this->getUimapPage($area, $page, $paramsDecorator);
        return $pageUimap->getMca($paramsDecorator);
    }

    /**
     * Return xpath which we need to click to open page
     *
     * @param string $area Application area
     * @param string $page UIMap page key
     * @param null|Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return mixed
     */
    public function getPageClickXpath($area, $page, $paramsDecorator = null)
    {
        $pageUimap = $this->getUimapPage($area, $page, $paramsDecorator);
        return $pageUimap->getClickXpath($paramsDecorator);
    }

}
