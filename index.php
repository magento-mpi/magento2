<?php
/**
 * Application entry point
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

try {
    require __DIR__ . '/app/bootstrap.php';
    /** @var $app Mage_Core_Model_App */
    $app = Mage::getObjectManager()->get('Mage_Core_Model_App');
    $params = $_SERVER;
    Mage::setApp($app);

    if (isset($params[Mage::INIT_OPTION_EDITION])) {
        Mage::setEdition($params[Mage::INIT_OPTION_EDITION]);
    }
    if (isset($params[Mage::INIT_OPTION_REQUEST])) {
        $app->setRequest($params[Mage::INIT_OPTION_REQUEST]);
    }
    if (isset($params[Mage::INIT_OPTION_RESPONSE])) {
        $app->setResponse($params[Mage::INIT_OPTION_RESPONSE]);
    }
    $app->run($params);
} catch (Exception $e) {
    Mage::printException($e);
}

 /**
 * Example - run a particular store or website:
 *
 * $params = $_SERVER;
 * $params['MAGE_RUN_CODE'] = 'website2';
 * $params['MAGE_RUN_TYPE'] = 'website';
 */
