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

/**
 * @codeCoverageIgnore
 */
class OptionProvider implements ArrayInterface
{
    /**
     * Permanent redirect code
     */
    const PERMANENT = 301;

    /**
     * Redirect code
     */
    const TEMPORARY = 302;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            0 => __('No'),
            self::TEMPORARY => __('Temporary (302)'),
            self::PERMANENT => __('Permanent (301)'),
        );
    }
}
