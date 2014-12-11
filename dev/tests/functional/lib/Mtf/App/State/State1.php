<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\App\State;

use Magento\Core\Test\Fixture\Config;

/**
 * Class State1
 * Example Application State class
 */
class State1 extends AbstractState
{
    /**
     * Configuration fixture
     *
     * @var Config
     */
    protected $config;

    /**
     * @construct
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        parent::apply();
        if (file_exists(dirname(dirname(dirname(MTF_BP))) . '/app/etc/config.php')) {
            $this->config->switchData('app_state1_configuration');
            $this->config->persist();
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Configuration Profile #1';
    }
}
