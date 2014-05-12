<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Repository;

/**
 * Class BannerRotatorWidget Repository
 *
 */
class BannerRotatorWidget extends Widget
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        parent::__construct($defaultConfig, $defaultData);

        $this->_data['default']['data']['type'] = 'magento_banner';
        $this->_data['default']['data']['fields']['title']['value'] = 'Banner Rotator %isolation%';
    }
}
