<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class with simple substitution parameters to values
 */
class Mage_Core_Model_Design_Fallback_Rule_Simple implements Mage_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * Constructor
     *
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->_pattern = $pattern;
    }

    /**
     * Get ordered list of folders to search for a file
     *
     * @param string $fileName - relative file name
     * @param array $params - array of parameters
     * @param array $themeList - ordered array of themes - current theme and all its parents
     * @return array of folders to perform a search
     */
    public function getPatternDirs($fileName, $params, $themeList)
    {
        if ((strpos($this->_pattern, '<module>') !== false) && empty($params['module'])) {
            return array();
        } else {
            return array(array('dir' => $this->_fillVariable($this->_pattern, $params), 'pattern' => $this->_pattern));
        }
    }

    /**
     * Get pattern with substituted data
     *
     * @param string $pattern
     * @param array $params
     * @return string
     * @throws InvalidArgumentException
     */
    protected function _fillVariable($pattern, $params)
    {
        if (preg_match_all('/<([a-zA-Z\_]+)>/', $pattern, $matches)) {
            foreach ($matches[1] as $placeholder) {
                if (!array_key_exists($placeholder, $params)) {
                    throw new InvalidArgumentException("Parameter '$placeholder' was not passed");
                }
                $pattern = str_replace('<' . $placeholder . '>', $params[$placeholder], $pattern);
            }
        }
        return $pattern;
    }
}
