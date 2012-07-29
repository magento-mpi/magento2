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

Magento_Profiler::start('mage');

Mage::setRoot();
$config = new Mage_Core_Model_Config();
$config->loadBase();
Mage::setConfig($config);

if (!file_exists($config->getOptions()->getEtcDir() . '/' . 'DiDefinition.php')) {
    $compiler = new \Zend\Di\Definition\CompilerDefinition();
    $compiler->getIntrospectionStrategy()->setMethodNameInclusionPatterns(array());
    $compiler->getIntrospectionStrategy()->setInterfaceInjectionInclusionPatterns(array());
    $compiler->getIntrospectionStrategy()->setUseAnnotations(false);
    $compiler->addDirectory($config->getOptions()->getAppDir());
    $compiler->compile();

    // Now, create a Definition class for this information
    $classGenerator = new Zend\Code\Generator\ClassGenerator();
    $classGenerator->setName('DiDefinition');
    $classGenerator->setExtendedClass('\Zend\Di\Definition\ArrayDefinition');
    $classGenerator->addMethod(
        '__construct',
        array(),
        \Zend\Code\Generator\MethodGenerator::FLAG_PUBLIC,
        'parent::__construct(' . var_export($compiler->toArrayDefinition(), true) . ');'
    );
    file_put_contents($config->getOptions()->getEtcDir() . '/DiDefinition.php', $classGenerator->generate());
} else {
    require_once $config->getOptions()->getEtcDir() . '/DiDefinition.php';
}

$definitions = new \Zend\Di\DefinitionList(new DiDefinition());
$factory = new Magento_ObjectManager_Zend(new Di($definitions), $config);
Mage::setObjectManager($factory);

Magento_Profiler::start('init');
/** @var $app Mage_Core_Model_App */
$app = $factory->create('Mage_Core_Model_App', array(
    'applicationConfig' => $config, 'applicationOptions' => array(), 'data' => array()));
Mage::setApp($app);
$app->initCurrentStore($mageRunCode, $mageRunType);
Magento_Profiler::stop('init');

Mage::setApp($app);
$request = new Mage_Core_Controller_Request_Http();
$request->setPathInfo();
$app->process($request);

Magento_Profiler::stop('mage');
