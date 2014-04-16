<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme data validation
 */
namespace Magento\Core\Model\Theme;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $data
     * @param bool $result
     * @param array $messages
     *
     * @covers \Magento\View\Design\Theme\Validator::validate
     * @dataProvider dataProviderValidate
     */
    public function testValidate(array $data, $result, array $messages)
    {
        /** @var $themeMock \Magento\Object */
        $themeMock = new \Magento\Object();
        $themeMock->setData($data);

        $validator = new \Magento\View\Design\Theme\Validator();

        $this->assertEquals($result, $validator->validate($themeMock));
        $this->assertEquals($messages, $validator->getErrorMessages());
    }

    public function dataProviderValidate()
    {
        return array(
            array(
                array(
                    'theme_code' => 'Magento/iphone',
                    'theme_title' => 'Iphone',
                    'theme_version' => '2.0.0.0',
                    'parent_theme' => array('default', 'default'),
                    'theme_path' => 'Magento/iphone',
                    'preview_image' => 'images/preview.png'
                ),
                true,
                array(),
            ),
            array(
                array(
                    'theme_code' => 'iphone#theme!!!!',
                    'theme_title' => 'Iphone',
                    'theme_version' => 'last theme version',
                    'parent_theme' => array('default', 'default'),
                    'theme_path' => 'magento_iphone',
                    'preview_image' => 'images/preview.png'
                ),
                false,
                array(
                    'theme_version' => array('Theme version has not compatible format.')
                ),
            ),
            array(
                array(
                    'theme_code' => 'iphone#theme!!!!',
                    'theme_title' => '',
                    'theme_version' => '',
                    'parent_theme' => array('default', 'default'),
                    'theme_path' => 'magento_iphone',
                    'preview_image' => 'images/preview.png'
                ),
                false,
                array(
                    'theme_version' => array('Field can\'t be empty'),
                    'theme_title' => array('Field title can\'t be empty')
                ),
            ),
        );
    }

}
