<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'preferences' => array(
        'Magento\Framework\Module\SomeInterface' => 'Magento\Framework\Module\ClassOne',
        'Magento\Framework\App\RequestInterface' => 'Magento\Framework\App\Request\Http\Proxy',
    ),
    'Magento\Framework\App\State' => array('arguments' => array('test name' => 'test value')),
    'Magento\Core\Model\Config\Modules' => array(
        'arguments' => array('test name' => 'test value'),
        'plugins' => array(
            'simple_modules_plugin' => array(
                'sortOrder' => 10,
                'disabled' => true,
                'instance' => 'Magento\Core\Model\Config\Modules\Plugin'
            ),
            'simple_modules_plugin_advanced' => array(
                'sortOrder' => 0,
                'instance' => 'Magento\Core\Model\Config\Modules\PluginAdvanced'
            ),
            'overridden_plugin' => array('sortOrder' => 30, 'disabled' => true)
        )
    ),
    'Magento\Framework\HTTP\Handler\Composite' => array(
        'shared' => false,
        'arguments' => array('test name' => 'test value')
    ),
    'customCacheInstance' => array(
        'shared' => true,
        'type' => 'Magento\Framework\App\Cache',
        'arguments' => array()
    ),
    'customOverriddenInstance' => array(
        'shared' => false,
        'arguments' => array()
    )
);
