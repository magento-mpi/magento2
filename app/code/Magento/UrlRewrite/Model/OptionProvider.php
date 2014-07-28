<?php
/**
 * URL Rewrite Option Provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model;

use Magento\Framework\Option\ArrayInterface;

class OptionProvider implements ArrayInterface
{
    const TEMPORARY = 302;
    const PERMANENT = 301;

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '' => __('No'),
            self::TEMPORARY => __('Temporary (302)'),
            self::PERMANENT => __('Permanent (301)'),
        );
    }
}
