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
Magento_Profiler::start('di_outer');
Magento_Profiler::start('di');
if (file_exists('var/di/definitions.php')) {
    $definition = new Zend\Di\Definition\ArrayDefinition(unserialize(file_get_contents('var/di/definitions.php')));
} else {
    $definition = new Zend\Di\Definition\RuntimeDefinition();
}
Magento_Profiler::stop('di');
$factory = new Magento_ObjectManager_Zend(new Di(new DefinitionList($definition)));
Mage::setObjectManager($factory);
Magento_Profiler::stop('di_outer');

Magento_Profiler::start('init');
/** @var $app Mage_Core_Model_App */
$app = $factory->create('Mage_Core_Model_App');
Mage::setApp($app);
Magento_Profiler::stop('init');

Mage::setApp($app);
$request = $factory->get('Mage_Core_Controller_Request_Http');
$app->process($request, $mageRunCode, $mageRunType);

Magento_Profiler::stop('mage');
