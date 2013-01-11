<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Callable class of application entry point
 */
class EntryPoint
{
    /**
     * @var Mage_Core_Controller_Request_Http|null
     */
    private $_request = null;

    /**
     * Entry point initialization
     *
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(Mage_Core_Controller_Request_Http $request = null){
        if (is_null($request)) {
            $request = new Mage_Core_Controller_Request_Http;
        }
        $this->_request = $request;
    }

    /**
     * Run application based on invariant configuration string
     *
     * Configuration parameter is invariant and must not change with new versions of the product
     *
     * @param string $appConfigString
     */
    public function __invoke($appConfigString)
    {
        $appConfig = unserialize($appConfigString);

        require_once __DIR__ . '/bootstrap.php';

        $baseConfig = !empty($appConfig['base_config']) ? $appConfig['base_config'] : array();
        $options = !empty($appConfig['options']) ? $appConfig['options'] : array();

        $appOptions = new Mage_Core_Model_App_Options($this->_request->getServer());
        $appRunOptions = array_merge(
            $appOptions->getRunOptions(),
            array(Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_DATA => $baseConfig),
            $options
        );

        $this->_run($appOptions->getRunCode(), $appOptions->getRunType(), $appRunOptions);
    }

    /**
     * @param string $runCode
     * @param string $runType
     * @param array $runOptions
     */
    protected function _run($runCode, $runType, array $runOptions = array())
    {
        Mage::run($runCode, $runType, $runOptions);
    }
}
