<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Backend\Block\Template;

/**
 * Class AbstractView
 */
abstract class AbstractView extends Template implements ViewInterface
{
    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * Getting JSON configuration data
     *
     * @return string
     */
    public function getConfigurationJson()
    {
        return json_encode($this->configuration);
    }
}
