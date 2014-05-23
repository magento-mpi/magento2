<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\App\Cache\TypeList
 */
namespace Magento\Framework\App\Cache;

class TypeListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Cache\TypeList
     */
    protected $_typeList;
    protected $_cache;
    protected $_typesArray;
    protected $_config;

    protected function setUp()
    {
        $this->_typesArray = [
            'config' => ['label' => 'Configuration', 'description' => 'System(config.xml, local.xml) and modules configuration files(config.xml, menu.xml).'],
            'layout' => ['label' => 'Layouts', 'description' => 'Layout building instructions.'],
            'block_html' => ['label' => 'Blocks HTML output', 'description' => 'Page blocks HTML'],
            'collections' => ['label' => 'Collections Data', 'description' => 'Collection data files.'],
            'eav' => ['label' => 'EAV types and attributes', 'description' => 'Entity types declaration cache.'],
            'config_integration' => ['label' => 'Integrations Configuration', 'description' => 'Integration configuration file.'],
            'full_page' => ['label' => 'Page Cache', 'description' => 'Full page caching.'],
            'translate' => ['label' => 'Translations', 'description' => 'Translation files.'],
            'config_webservice' => ['label' => 'Web Services Configuration', 'description' => 'REST and SOAP configurations, generated WSDL file.'],
            'config_integration_api' => ['label' => 'Integrations API Configuration', 'description' => 'Integrations API configuration file.']
        ];
        $this->_config = $this->getMock('Magento\Framework\Cache\ConfigInterface', ['getTypes', 'getType'], [], '', false);
        $this->_config->expects($this->any())->method('getTypes')->will($this->returnValue($this->_typesArray));

        $cacheState = $this->getMock('Magento\Framework\App\Cache\StateInterface', ['isEnabled', 'setEnabled', 'persist'], [], '', false);
        $cacheState->expects($this->any())->method('isEnabled')->will($this->returnValue(true));
        $cacheBlockMock = $this->getMock('Magento\Framework\App\Cache\Type\Block', [], [], '', false);
        $factory = $this->getMock('Magento\Framework\App\Cache\InstanceFactory', ['get'], [], '', false);
        $factory->expects($this->any())->method('get')->with('Magento\Framework\App\Cache\Type\Block')->will($this->returnValue($cacheBlockMock));
        $this->_cache = $this->getMock('Magento\Framework\App\CacheInterface', ['load', 'getFrontend', 'save', 'remove', 'clean'], [], '', false);

        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_typeList = $objectHelper->getObject('Magento\Framework\App\Cache\TypeList',
            [
                'config' => $this->_config,
                'cacheState' => $cacheState,
                'factory' => $factory,
                'cache' => $this->_cache
            ]
        );
    }

    public function testGetTypes()
    {
        $this->assertArrayHasKey('eav', $this->_typeList->getTypes());
        $this->assertArrayHasKey('translate', $this->_typeList->getTypes());
        $this->assertArrayHasKey('config_integration_api', $this->_typeList->getTypes());
    }

    public function testGetInvalidated()
    {
        $this->_cache->expects($this->any())->method('load')->with('core_cache_invalidate')->will($this->returnValue(serialize($this->_typesArray)));
        $this->assertArrayHasKey('config', $this->_typeList->getInvalidated());
        $this->assertArrayHasKey('collections', $this->_typeList->getInvalidated());
        $this->assertArrayHasKey('full_page', $this->_typeList->getInvalidated());
    }

    public function testInvalidate()
    {
        $this->_cache->expects($this->any())->method('load')->with('core_cache_invalidate')->will($this->returnValue(serialize($this->_typesArray)));
        $this->_typesArray['config_integration'] = 1;
        $this->_cache->expects($this->once())->method('save')->with(serialize($this->_typesArray), 'core_cache_invalidate');
        $this->_typeList->invalidate('config_integration');
    }

    public function testNotInvalidate()
    {
        $this->_cache->expects($this->any())->method('load')->with('core_cache_invalidate')->will($this->returnValue([]));
        $this->_cache->expects($this->once())->method('save')->with(serialize(['config_integration' => 1]), 'core_cache_invalidate');
        $this->_typeList->invalidate('config_integration');
    }

    public function testCleanType()
    {
        $this->_cache->expects($this->any())->method('load')->with('core_cache_invalidate')->will($this->returnValue(serialize($this->_typesArray)));
        $this->_config->expects($this->any())->method('getType')->with('block_html')->will($this->returnValue(['instance' => 'Magento\Framework\App\Cache\Type\Block']));
        unset($this->_typesArray['block_html']);
        $this->_cache->expects($this->once())->method('save')->with(serialize($this->_typesArray), 'core_cache_invalidate');
        $this->_typeList->cleanType('block_html');
    }
}
