<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\FileResolver
     */
    protected $_model;

    protected function setUp()
    {
        $appConfigDir = __DIR__ . '/FileResolver/_files/app/etc';

        $applicationDirs = $this->getMock('Magento\Filesystem', array(), array('getPath'), '', false);
        $applicationDirs->expects($this->any())
            ->method('getPath')
            ->with(\Magento\Filesystem::CONFIG)
            ->will($this->returnValue($appConfigDir));

        $moduleReader = $this->getMock('Magento\Module\Dir\Reader', array(),
            array('getConfigurationFiles'), '', false);
        $moduleReader->expects($this->any())
            ->method('getConfigurationFiles')
            ->will($this->returnValueMap(
                array(
                    array(
                        'adminhtml/di.xml',
                        array(
                            'app/code/Custom/FirstModule/adminhtml/di.xml',
                            'app/code/Custom/SecondModule/adminhtml/di.xml',
                        )
                    ),
                    array(
                        'frontend/di.xml',
                        array(
                            'app/code/Custom/FirstModule/frontend/di.xml',
                            'app/code/Custom/SecondModule/frontend/di.xml',
                        )
                    ),
                    array(
                        'di.xml',
                        array(
                            'app/code/Custom/FirstModule/di.xml',
                            'app/code/Custom/SecondModule/di.xml',
                        )
                    ),
                )
            ));
        $this->_model = new \Magento\Core\Model\Config\FileResolver($moduleReader, $applicationDirs);
    }

    /**
     * @param array $expectedResult
     * @param string $scope
     * @param string $filename
     * @dataProvider getMethodDataProvider
     */
    public function testGet(array $expectedResult, $scope, $filename)
    {
        $this->assertEquals($expectedResult, $this->_model->get($filename, $scope));
    }

    /**
     * @return array
     */
    public function getMethodDataProvider()
    {
        $appConfigDir = __DIR__ . '/FileResolver/_files/app/etc';
        return array(
            array(
                array(
                    $appConfigDir . '/config.xml',
                    $appConfigDir . '/custom/config.xml',
                ),
                'primary',
                'config.xml',
            ),
            array(
                array(
                    'app/code/Custom/FirstModule/di.xml',
                    'app/code/Custom/SecondModule/di.xml',
                ),
                'global',
                'di.xml',
            ),
            array(
                array(
                    'app/code/Custom/FirstModule/frontend/di.xml',
                    'app/code/Custom/SecondModule/frontend/di.xml',
                ),
                'frontend',
                'di.xml',
            ),
            array(
                array(
                    'app/code/Custom/FirstModule/adminhtml/di.xml',
                    'app/code/Custom/SecondModule/adminhtml/di.xml',
                ),
                'adminhtml',
                'di.xml',
            ),
        );
    }
}
