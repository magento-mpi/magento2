<?php
/**
 * URL Rewrite Option Provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Model;

use Magento\Framework\Option\ArrayInterface;

class OptionProvider implements ArrayInterface
{
    const TEMPORARY = 'R';

    const PERMANENT = 'RP';

    /**
     * @var array|null
     */
    protected $_options = null;

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                '' => __('No'),
                self::TEMPORARY => __('Temporary (302)'),
                self::PERMANENT => __('Permanent (301)'),
            );
        }
        return $this->_options;
    }

    /**
     * Get options list (redirects only)
     *
     * @return string[]
     */
    public function getRedirectOptions()
    {
        return array(self::TEMPORARY, self::PERMANENT);
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
