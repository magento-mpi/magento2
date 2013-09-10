<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Annotation_AppArea
{
    const ANNOTATION_NAME = 'magentoAppArea';

    /**
     * @var Magento_Test_Application
     */
    private $_application;

    /**
     * List of allowed areas
     *
     * @var array
     */
    private $_allowedAreas = array(
        Magento_Core_Model_App_Area::AREA_GLOBAL,
        Magento_Core_Model_App_Area::AREA_ADMINHTML,
        Magento_Core_Model_App_Area::AREA_FRONTEND,
        'install',
        'webapi_rest',
        'webapi_soap',
        'cron',
    );

    /**
     * @param Magento_Test_Application $application
     */
    public function __construct(Magento_Test_Application $application)
    {
        $this->_application = $application;
    }

    /**
     * Get current application area
     *
     * @param array $annotations
     * @return string
     * @throws Magento_Exception
     */
    protected function _getTestAppArea($annotations)
    {
        $area = isset($annotations['method'][self::ANNOTATION_NAME])
                    ? current($annotations['method'][self::ANNOTATION_NAME])
                    : (isset($annotations['class'][self::ANNOTATION_NAME])
                        ? current($annotations['class'][self::ANNOTATION_NAME])
                        : Magento_Test_Application::DEFAULT_APP_AREA);

        if (false == in_array($area, $this->_allowedAreas)) {
            throw new Magento_Exception(
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
