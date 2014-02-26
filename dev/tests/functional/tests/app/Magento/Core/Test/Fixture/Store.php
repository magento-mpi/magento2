<?php
/**
 * Store fixture
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Fixture;
use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;

class Store extends DataFixture
{
    /**
     * @param \Mtf\System\Config $configuration
     * @param array $placeholders
     */
    public function __construct(\Mtf\System\Config $configuration, array $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);
        $this->_placeholders = $placeholders;
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'group' => array(
                    'value' => 'Main Website Store',
                    'input' => 'select'
                ),
                'name' => array(
                    'value' => 'DE%isolation%'
                ),
                'code' => array(
                    'value' => 'de%isolation%'
                ),
                'is_active' => array(
                    'value' => 'Enabled',
                    'input' => 'select',
                ),
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCoreCustomStore($this->_dataConfig, $this->_data);
    }


    /**
     * Create Store
     *
     * @return Store
     */
    public function persist()
    {
        return Factory::getApp()->magentoCoreCreateStore($this);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('fields/name/value');
    }
}
