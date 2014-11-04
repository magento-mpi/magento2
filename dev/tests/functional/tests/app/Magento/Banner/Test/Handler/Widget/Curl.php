<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Handler\Widget;

/**
 * Curl handler for creating widgetInstance/frontendApp.
 */
class Curl extends \Magento\Widget\Test\Handler\Widget\Curl
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $additionalMappingData = [
        'code' => [
            'Banner Rotator' => 'magento_banner'
        ],
    ];

    /**
     * Widget Instance Template.
     *
     * @var string
     */
    protected $widgetInstanceTemplate = 'widget/block.phtml';

    /**
     * @constructor
     */
    public function __construct()
    {
        $this->mappingData = array_merge($this->mappingData, $this->additionalMappingData);
    }
}
