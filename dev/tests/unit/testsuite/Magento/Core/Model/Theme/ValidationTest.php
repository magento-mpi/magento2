<?php
/**
 * {license_notice}
 *
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
     * @covers \Magento\Framework\View\Design\Theme\Validator::validate
     * @dataProvider dataProviderValidate
     */
    public function testValidate(array $data, $result, array $messages)
    {
        /** @var $themeMock \Magento\Framework\Object */
        $themeMock = new \Magento\Framework\Object();
        $themeMock->setData($data);

        $validator = new \Magento\Framework\View\Design\Theme\Validator();

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
                    'theme_title' => '',
                    'parent_theme' => array('default', 'default'),
                    'theme_path' => 'magento_iphone',
                    'preview_image' => 'images/preview.png'
                ),
                false,
                array(
                    'theme_title' => array('Field title can\'t be empty')
                ),
            ),
        );
    }

}
