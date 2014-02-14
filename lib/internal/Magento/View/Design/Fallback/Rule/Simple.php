<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Fallback\Rule;

/**
 * Class with simple substitution parameters to values
 */
class Simple implements RuleInterface
{
    /**
     * Optional params for rule
     *
     * @var array
     */
    protected $optionalParams;

    /**
     * Pattern for a simple rule
     *
     * @var string
     */
    protected $pattern;

    /**
     * @param string $pattern
     * @param array $optionalParams
     */
    public function __construct($pattern, array $optionalParams = array())
    {
        $this->pattern = $pattern;
        $this->optionalParams = $optionalParams;
    }

    /**
     * Get ordered list of folders to search for a file
     *
     * @param array $params array of parameters
     * @return array folders to perform a search
     * @throws \InvalidArgumentException
     */
    public function getPatternDirs(array $params)
    {
        $pattern = $this->pattern;
        if (preg_match_all('/<([a-zA-Z\_]+)>/', $pattern, $matches)) {
            foreach ($matches[1] as $placeholder) {
                if (empty($params[$placeholder])) {
                    if (in_array($placeholder, $this->optionalParams)) {
                        return array();
                    } else {
                        throw new \InvalidArgumentException("Required parameter '$placeholder' was not passed");
                    }
                }
                $pattern = str_replace('<' . $placeholder . '>', $params[$placeholder], $pattern);
            }
        }
        return array($pattern);
    }
}
