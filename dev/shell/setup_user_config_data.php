<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

use Magento\Framework\App\State as AppState;
use Magento\Store\Model\Store;
use Magento\Core\Helper\Data;
use Magento\Directory\Model\Currency;
use Magento\Backend\Model\Url;

require __DIR__ . '/../../app/bootstrap.php';
$params = $_SERVER;
if (!isset($params[AppState::PARAM_MODE])) {
    $params[AppState::PARAM_MODE] = AppState::MODE_DEVELOPER;
}
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);

$args = explode('}', trim($params['argv'][1], '}'));
$configData = [];
foreach ($args as $val) {
    $split = explode('{', $val);
    $configData[trim($split[0])] = trim($split[1]);
}

foreach ($configData as $path => $key) {
    switch($path){
        case Store::XML_PATH_UNSECURE_BASE_URL:
            $baseUrlSecureConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Baseurl'
            );
            $baseUrlSecureConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Store::XML_PATH_UNSECURE_BASE_URL
                )->setValue(
                    $key
                )->save();
            break;
        case Store::XML_PATH_SECURE_BASE_URL:
            $baseUrlInsecureConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Baseurl'
            );
            $baseUrlInsecureConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Store::XML_PATH_SECURE_BASE_URL
                )->setValue(
                    $key
                )->save();
            break;
        case Store::XML_PATH_SECURE_IN_FRONTEND:
            $frontendSecureUrlConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Secure'
            );
            $frontendSecureUrlConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Store::XML_PATH_SECURE_IN_FRONTEND
                )->setValue(
                    $key
                )->save();
            break;
        case Store::XML_PATH_SECURE_IN_ADMINHTML:
            $backendSecureUrlConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Secure'
            );
            $backendSecureUrlConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Store::XML_PATH_SECURE_IN_ADMINHTML
                )->setValue(
                    $key
                )->save();
            break;
        case Data::XML_PATH_DEFAULT_TIMEZONE:
            $timeConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Locale\Timezone'
            );
            $timeConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Data::XML_PATH_DEFAULT_TIMEZONE
                )->setValue(
                    $key
                )->save();
            break;
        case Currency::XML_PATH_CURRENCY_BASE:
            $baseCurrencyConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Currency\Base'
            );
            $baseCurrencyConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Currency::XML_PATH_CURRENCY_BASE
                )->setValue(
                    $key
                )->save();
            break;
        case Currency::XML_PATH_CURRENCY_DEFAULT:
            $defaultCurrencyConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Currency\DefaultCurrency'
            );
            $defaultCurrencyConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Currency::XML_PATH_CURRENCY_DEFAULT
                )->setValue(
                    $key
                )->save();
            break;
        case Currency::XML_PATH_CURRENCY_ALLOW:
            $allowCurrencyConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Currency\Allow'
            );
            $allowCurrencyConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Currency::XML_PATH_CURRENCY_ALLOW
                )->setValue(
                    $key
                )->save();
            break;
        case Url::XML_PATH_USE_SECURE_KEY:
            $adminKeyConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Admin\Usesecretkey'
            );
            $adminKeyConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Url::XML_PATH_USE_SECURE_KEY
                )->setValue(
                    $key
                )->save();
            break;
        case Data::XML_PATH_DEFAULT_LOCALE:
            $localeConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Store'
            );
            $localeConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Data::XML_PATH_DEFAULT_LOCALE
                )->setValue(
                    $key
                )->save();
            break;
        case Store::XML_PATH_USE_REWRITES:
            $storeConfig = $bootstrap->getObjectManager()->create(
                '\Magento\Backend\Model\Config\Backend\Store'
            );
            $storeConfig->setScope(
                'default'
            )->setScopeId(
                    '0'
                )->setPath(
                    Store::XML_PATH_USE_REWRITES
                )->setValue(
                    $key
                )->save();
            break;
    }
}
