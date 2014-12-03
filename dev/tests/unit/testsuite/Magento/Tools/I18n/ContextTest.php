<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n;

use Magento\Tools\I18n\Context;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Context
     */
    protected $context;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->context = $objectManagerHelper->getObject('Magento\Tools\I18n\Context');
    }

    /**
     * @param array $context
     * @param string $path
     * @dataProvider dataProviderContextByPath
     */
    public function testGetContextByPath($context, $path)
    {
        $this->assertEquals($context, $this->context->getContextByPath($path));
    }

    /**
     * @return array
     */
    public function dataProviderContextByPath()
    {
        return array(
            array(array(Context::CONTEXT_TYPE_MODULE, 'Magento_Module'), '/app/code/Magento/Module/Block/Test.php'),
            array(array(Context::CONTEXT_TYPE_THEME, 'area/theme/test.phtml'), '/app/design/area/theme/test.phtml'),
            array(array(Context::CONTEXT_TYPE_LIB, 'lib/web/module/test.phtml'), '/lib/web/module/test.phtml'),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid path given: "invalid_path".
     */
    public function testGetContextByPathWithInvalidPath()
    {
        $this->context->getContextByPath('invalid_path');
    }

    /**
     * @param string $path
     * @param array $context
     * @dataProvider dataProviderPathToLocaleDirectoryByContext
     */
    public function testBuildPathToLocaleDirectoryByContext($path, $context)
    {
        $this->assertEquals($path, $this->context->buildPathToLocaleDirectoryByContext($context[0], $context[1]));
    }

    /**
     * @return array
     */
    public function dataProviderPathToLocaleDirectoryByContext()
    {
        return array(
            array('app/code/Magento/Module/i18n/', array(Context::CONTEXT_TYPE_MODULE, 'Magento_Module')),
            array('app/design/theme/test.phtml/i18n/', array(Context::CONTEXT_TYPE_THEME, 'theme/test.phtml')),
            array('lib/web/i18n/', array(Context::CONTEXT_TYPE_LIB, 'lib/web/module/test.phtml')),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid context given: "invalid_type".
     */
    public function testBuildPathToLocaleDirectoryByContextWithInvalidType()
    {
        $this->context->buildPathToLocaleDirectoryByContext('invalid_type', 'Magento_Module');
    }
}
