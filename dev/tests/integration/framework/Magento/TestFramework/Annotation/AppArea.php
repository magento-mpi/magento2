<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_TestFramework_Annotation_AppArea
{
    const ANNOTATION_NAME = 'magentoAppArea';

    /**
     * @var Magento_TestFramework_Application
     */
    private $_application;

    /**
     * List of allowed areas
     *
     * @var array
     */
    private $_allowedAreas = array(
        \Magento\Core\Model\App\Area::AREA_GLOBAL,
        \Magento\Core\Model\App\Area::AREA_ADMINHTML,
        \Magento\Core\Model\App\Area::AREA_FRONTEND,
        'install',
        'webapi',
        'cron',
    );

    /**
     * @param Magento_TestFramework_Application $application
     */
    public function __construct(Magento_TestFramework_Application $application)
    {
        $this->_application = $application;
    }

    /**
     * Get current application area
     *
     * @param array $annotations
     * @return string
     * @throws \Magento\Exception
     */
    protected function _getTestAppArea($annotations)
    {
        $area = isset($annotations['method'][self::ANNOTATION_NAME])
                    ? current($annotations['method'][self::ANNOTATION_NAME])
                    : (isset($annotations['class'][self::ANNOTATION_NAME])
                        ? current($annotations['class'][self::ANNOTATION_NAME])
                        : Magento_TestFramework_Application::DEFAULT_APP_AREA);

        if (false == in_array($area, $this->_allowedAreas)) {
            throw new \Magento\Exception(
                'Invalid "@magentoAppArea" annotation, can be "' . implode('", "', $this->_allowedAreas) . '" only.'
            );
        }

        return $area;
    }

    /**
     * Start test case event observer
     *
     * @param PHPUnit_Framework_TestCase $test
     */
    public function startTest(PHPUnit_Framework_TestCase $test)
    {
        $area = $this->_getTestAppArea($test->getAnnotations());
        if ($this->_application->getArea() !== $area) {
            $this->_application->reinitialize();

            if ($this->_application->getArea() !== $area) {
                $this->_application->loadArea($area);
            }
        }
    }
}