<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Repository;

/**
 * Class BannerRotatorWidget Repository
 *
 * @package Magento\Widget\Test\Repository
 */
class BannerRotatorWidget extends Widget
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        parent::__construct($defaultConfig, $defaultData);

        $this->_data['default']['data']['type'] = 'magento_banner';
        $this->_data['default']['data']['fields']['title']['value'] = 'Test Banner Rotator';
    }
}
