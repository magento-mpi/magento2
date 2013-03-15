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
     * Optional params for rule
     *
     * @var array
     */
    protected $_optionalParams;

    /**
     * Constructor
     *
     * @param string $pattern
     * @param array $optionalParams
     */
    public function __construct($pattern, $optionalParams = array())
    {
        $this->_pattern = str_replace('/', DIRECTORY_SEPARATOR, $pattern);
        $this->_optionalParams = $optionalParams;
    }

    /**
     * Get ordered list of folders to search for a file
     *
     * @param array $params - array of parameters
     * @return array of folders to perform a search
     * @throws InvalidArgumentException
     */
    public function getPatternDirs($params)
    {
        $pattern = $this->_pattern;
        if (preg_match_all('/<([a-zA-Z\_]+)>/', $pattern, $matches)) {
            foreach ($matches[1] as $placeholder) {
                if (empty($params[$placeholder])) {
                    if (in_array($placeholder, $this->_optionalParams)) {
                        return array();
                    } else {
                        throw new InvalidArgumentException("Required parameter '$placeholder' was not passed");
                    }
                }
                $pattern = str_replace('<' . $placeholder . '>', $params[$placeholder], $pattern);
            }
        }

        return array(array(
            'dir' => str_replace('/', DIRECTORY_SEPARATOR, $pattern),
            'pattern' => str_replace('/', DIRECTORY_SEPARATOR, $this->_pattern)
        ));
    }
}
