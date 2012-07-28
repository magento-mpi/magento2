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

$factory = new Magento_ObjectManager_Zend(new Di());
Mage::setObjectManager($factory);
Mage::run($mageRunCode, $mageRunType);
