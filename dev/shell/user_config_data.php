<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../../app/bootstrap.php';

$usage = 'Usage: php -f user_config_data.php -- --noOfConfigDatasets=<number> - number of configuration datasets
    ' . '[each dataset contains four following parameters in order:'
    . ' you can use \'1\' for either \'website\' or \'store\' for one dataset;'
    . ' however, if both are \'0\', \'default\' is considered]
    ' . ' --website="0|1"  - configuration is website specific
    ' . ' --store="0|1"  - configuration is store specific
    ' . ' --path=<string> - path of the specified data group. e.g. \'web/unsecure/base_url\'
    ' . ' --value=<string> -  value for the path specified. e.g. \'http://127.0.0.1/\'
    ' . '###Example Usage: php -f user_config_data.php -- --noOfConfigDatasets="2" --website="0" --store="0"'
    . ' --path="web/seo/use_rewrites" --value="1" --website="0" --store="0" --path="web/unsecure/base_url"'
    . '--value="http://127.0.0.1/"' . PHP_EOL;

$longOpts = [
    'help',
    'noOfConfigDatasets::',
    'website::',
    'store::',
    'path:',
    'value:'
];

$opt = getopt('', $longOpts);
if (empty($opt) || isset($opt['help'])) {
    echo $usage;
    exit(0);
}

$params = $_SERVER;
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
/** @var \Magento\Backend\Model\Config $configModel  */
$configModel = $bootstrap->getObjectManager()->create(
    '\Magento\Backend\Model\Config'
);

for ($i = 0; $i < $opt['noOfConfigDatasets']; $i++) {
    $configData = [];
    $configData['website'] = isset($opt['website'][$i]) ? $opt['website'][$i] : null;
    $configData['store'] = isset($opt['store'][$i]) ? $opt['store'][$i] : null;
    $pathParts = explode('/', trim(str_replace('\\', '/', $opt['path'][$i]), '/'));
    $configData['section'] = $pathParts[0];
    $groups = [];
    $groups[$pathParts[1]]['fields'][$pathParts[2]]['value'] = $opt['value'][$i];
    $configData['groups'] = $groups;
    $configModel->addData($configData);
    $configModel->save();
}
