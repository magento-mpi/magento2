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
 * UIMap helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Uimap extends Mage_Selenium_Helper_Abstract
{
    /**
     * Uimap data
     * @var array
     */
    protected $_uimapData = array();
    protected $_uimapIncludeData = array();
    protected $_uimapFilesData = array();
    protected $_uimapPagesMca = array();

    /**
     * Initialize process
     */
    protected function _init()
    {
        $config = $this->getConfig()->getHelper('config')->getConfigFramework();
        $this->_loadUimapIncludeData();
        $this->_loadUimapFilesData(
            $this->getConfig()->getHelper('config')->getFixturesFallbackOrder(),
            $this->getConfig()->getConfigUimap()
        );
        if ($config['load_all_uimaps']) {
            $this->_loadUimapData();
        }
    }

    /**
     * @return Mage_Selenium_Helper_Uimap
     */
    private function _loadUimapIncludeData()
    {
        $includeElements = array();
        //Form include elements array
        foreach ($this->getConfig()->getConfigUimapInclude() as $area => $files) {
            $includeElements[$area] = array();
            foreach ($files as $file) {
                $pages = $this->getConfig()->getHelper('file')->loadYamlFile($file);
                //Skip if file is empty
                if (!$pages) {
                    continue;
                }
                foreach ($pages as $content) {
                    //Skip if page content is empty
                    if (!$content) {
                        continue;
                    }
                    $this->_mergeUimapIncludes($includeElements[$area], $content);
                }
            }
        }
        $this->_uimapIncludeData = $includeElements;
        return $this;
    }

    /**
     * @param array $codePoolNames
     * @param array $configUimap
     *
     * @return Mage_Selenium_Helper_Uimap
     */
    private function _loadUimapFilesData(array $codePoolNames, array $configUimap)
    {
        $baseCodePoolName = array_shift($codePoolNames);
        if (isset($configUimap[$baseCodePoolName])) {
            foreach ($configUimap[$baseCodePoolName] as $area => $areaFiles) {
                foreach ($areaFiles as $file) {
                    $explode = explode(DIRECTORY_SEPARATOR, $file);
                    $fileName = trim(end($explode), '.yml');
                    $this->_uimapFilesData[$area][$fileName][] = $file;
                    foreach ($codePoolNames as $codePoolName) {
                        $additionalFile = str_replace($baseCodePoolName, $codePoolName, $file);
                        if (isset($configUimap[$codePoolName][$area])
                            && in_array($additionalFile, $configUimap[$codePoolName][$area])
                        ) {
                            $keyToDelete = array_search($additionalFile, $configUimap[$codePoolName][$area]);
                            $this->_uimapFilesData[$area][$fileName][] = $additionalFile;
                            unset($configUimap[$codePoolName][$area][$keyToDelete]);
                        }
                    }
                }
            }
            unset($configUimap[$baseCodePoolName]);
        }
        if (!empty($configUimap)) {
            $this->_loadUimapFilesData($codePoolNames, $configUimap);
        }
        return $this;
    }

    /**
     * Load and merge data files
     * @return Mage_Selenium_Helper_Uimap
     */
    private function _loadUimapData()
    {
        foreach ($this->_uimapFilesData as $area => $areaFiles) {
            foreach ($areaFiles as $files) {
                $baseFile = array_shift($files);
                $pages = $this->getConfig()->getHelper('file')->loadYamlFile($baseFile);
                foreach ($files as $file) {
                    $additionalPages = $this->getConfig()->getHelper('file')->loadYamlFile($file);
                    $pages = $this->_mergeUimapPages($pages, $additionalPages);
                }
                foreach ($pages as $pageKey => $content) {
                    //Skip if page content is empty
                    if (!$content) {
                        continue;
                    }
                    if (isset($this->_uimapIncludeData[$area])) {
                        $this->_mergeUimapIncludes($content, $this->_uimapIncludeData[$area]);
                    }
                    $this->_uimapData[$area][$pageKey] = new Mage_Selenium_Uimap_Page($pageKey, $content);
                    $this->_uimapPagesMca[$area][$pageKey] = $content['mca'];
                }
            }
        }
        return $this;
    }

    private function _mergeUimapPages(array $pageMergeTo, array $pageMergeFrom)
    {
        foreach ($pageMergeFrom as $pageName => $content) {
            //Skip if page content is empty for current project
            if (!$content) {
                continue;
            }
            if (isset($pageMergeTo[$pageName])) {
                $this->_mergeUimapIncludes($pageMergeTo[$pageName], $content);
            } else {
                $pageMergeTo[$pageName] = $content;
            }
        }
        return $pageMergeTo;
    }

    /**
     * Merge uimap elements
     *
     * @param array $replaceArrayTo
     * @param array $replaceArrayFrom
     *
     * @return array
     */
    private function _mergeUimapIncludes(array &$replaceArrayTo, array $replaceArrayFrom)
    {
        foreach ($replaceArrayFrom as $key => $value) {
            if (is_array($value)) {
                if (!empty($replaceArrayTo[$key])) {
                    if (is_string($key)) {
                        $this->_mergeUimapIncludes($replaceArrayTo[$key], $value);
                    } else {
                        list($keyFrom) = array_keys($value);
                        $keysTo = array();
                        $replaceArrayToKeys = array_keys($replaceArrayTo);
                        foreach ($replaceArrayToKeys as $number) {
                            $replaceArrayToNumber = array_keys($replaceArrayTo[$number]);
                            foreach ($replaceArrayToNumber as $name) {
                                $keysTo[$number] = $name;
                            }
                        }
                        if (in_array($keyFrom, $keysTo)) {
                            $ddd = array_search($keyFrom, $keysTo);
                            $this->_mergeUimapIncludes($replaceArrayTo[$ddd], $value);
                        } else {
                            $replaceArrayTo[] = $value;
                        }
                    }
                } else {
                    $replaceArrayTo[$key] = $value;
                }
            } else {
                if ($value == null) {
                    unset($replaceArrayTo[$key]);
                } else {
                    $replaceArrayTo[$key] = $value;
                }
            }
        }
    }

    /**
     * Retrieve array with UIMap data
     *
     * @param string $area Application area
     *
     * @return array
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
     * @return Mage_Selenium_Uimap_Page
     * @throws OutOfRangeException
     */
    public function getUimapPage($area, $pageKey, $paramsDecorator = null)
    {
        $areaUimaps = $this->getAreaUimaps($area);
        if (!array_key_exists($pageKey, $areaUimaps)) {
            throw new OutOfRangeException('Cannot find page "' . $pageKey . '" in area "' . $area . '"');
        }
        /** @var $page Mage_Selenium_Uimap_Page */
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
     * @param string $mca a part of current URL opened in browser
     * @param null|Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return Mage_Selenium_Uimap_Page
     * @throws OutOfRangeException
     */
    public function getUimapPageByMca($area, $mca, $paramsDecorator = null)
    {
        $mca = trim($mca, ' /\\');
        $appropriatePages = array();
        foreach ($this->_uimapPagesMca[$area] as $pageName => $pageMca) {
            $pageMca = preg_quote(trim($pageMca, ' /\\'));
            if ($paramsDecorator) {
                $pageMca = $paramsDecorator->replaceParametersWithRegexp($pageMca);
            }
            if (preg_match(';^' . $pageMca . '$;', $mca)) {
                $appropriatePages[] = $pageName;
            }
        }
        if (count($appropriatePages) == 1) {
            $pageName = array_shift($appropriatePages);
            return $this->_uimapData[$area][$pageName];
        }
        //Get mca with actual modifications if count($appropriatePages) > 1
        foreach ($appropriatePages as $pageName) {
            /** @var $page Mage_Selenium_Uimap_Page */
            $page = $this->_uimapData[$area][$pageName];
            $pageMca = trim($page->getMca($paramsDecorator), ' /\\');
            if ($pageMca === $mca) {
                $page->assignParams($paramsDecorator);
                return $page;
            }
        }
        throw new OutOfRangeException('Cannot find page with mca "' . $mca . '" in "' . $area . '" area');
    }

    /**
     * Get URL of the specified page
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
     * Get Page Mca
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
     * Get XPath that opens the specified page on click
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
