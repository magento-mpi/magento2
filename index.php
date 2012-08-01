<?php
use Zend\Di\Di,
    Zend\Di\DefinitionList;
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once 'app/bootstrap.php';

Magento_Profiler::enable();
Magento_Profiler::registerOutput(new Magento_Profiler_Output_Html());

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';
/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
/* Additional local.xml file from environment variable */
$options = array();
if (!empty($_SERVER['MAGE_LOCAL_CONFIG'])) {
    $options['local_config'] = $_SERVER['MAGE_LOCAL_CONFIG'];
}

Magento_Profiler::start('mage');

Mage::setRoot();
$definitions = null;
if (!file_exists(__DIR__ . '/var/di/di-serialized.php')) {
    $definitions = new \Zend\Di\DefinitionList(array());
} else {
    $definitions = unserialize(file_get_contents(__DIR__ . '/var/di/di-serialized.php'));
}

$di = new Di($definitions);
$factory = new Magento_ObjectManager_Zend(
    $di,
    realpath(__DIR__ . '/app/code/core'),
    __DIR__ . '/var/di/'
);


$config = $factory->get('Mage_Core_Model_Config');
$config->loadBase();
$factory->setParameters(
    'Mage_Core_Model_Cache',
    array(
        'cacheOptions' => $config->getNode('global/cache')->asArray(),
    )
);
Mage::setObjectManager($factory);

Mage::setConfig($config);

Magento_Profiler::start('init');
/** @var $app Mage_Core_Model_App */
$app = $factory->create('Mage_Core_Model_App', array(
    'applicationConfig' => $config, 'applicationOptions' => array(), 'data' => array()));
Mage::setApp($app);
Magento_Profiler::stop('init');

Mage::setApp($app);
$request = new Mage_Core_Controller_Request_Http();
$app->process($request, $mageRunCode, $mageRunType);

Magento_Profiler::stop('mage');

if (!file_exists(__DIR__ . '/var/di/di-serialized.php')) {
    $result = $di->definitions()->serialize();
    file_put_contents(__DIR__ . '/var/di/di-serialized.php', $result);
}
