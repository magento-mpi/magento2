<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$themeOne = new Varien_Object(array(
    'area' => 'area_one',
    'theme_path' => 'fixture/theme_one',
));
$themeTwo = new Varien_Object(array(
    'area' => 'area_one',
    'theme_path' => 'fixture/theme_two',
));
$themeThree = new Varien_Object(array(
    'area' => 'area_two',
    'theme_path' => 'fixture/theme_three',
));

return array(
    'fixture_one' => array(
        'theme' => $themeOne,
        'pattern_dir_map' => array(
            array('namespace' => '%namespace%', 'module' => '%module%', 'area' => 'area_one', 'theme' => $themeOne),
            array(
                '/base/dir/area_one/fixture/theme_one',
                '/base/dir/area_one/fixture/theme_one/%namespace%_%module%',
            ),
        ),
        'filesystem_glob_map' => array(
            '/base/dir/area_one/fixture/theme_one/', '*_*', array('/base/dir/area_one/fixture/theme_one/Mage_Core')
        ),
        'expected_result' => array(
            array(
                'source' => '/base/dir/area_one/fixture/theme_one/Mage_Core',
                'destination' => 'area_one/fixture/theme_one/Mage_Core',
            ),
            array(
                'source' => '/base/dir/area_one/fixture/theme_one',
                'destination' => 'area_one/fixture/theme_one',
            ),
        ),
    ),
    'fixture_two' => array(
        'theme' => $themeTwo,
        'pattern_dir_map' => array(
            array('namespace' => '%namespace%', 'module' => '%module%', 'area' => 'area_one', 'theme' => $themeTwo),
            array(
                '/base/dir/area_one/fixture/theme_two',
                '/base/dir/area_one/fixture/theme_two/%namespace%_%module%',
            ),
        ),
        'filesystem_glob_map' => array(
            '/base/dir/area_one/fixture/theme_two/', '*_*',
            array(
                '/base/dir/area_one/fixture/theme_two/Fixture_ModuleOne',
                '/base/dir/area_one/fixture/theme_two/Fixture_ModuleTwo',
            )
        ),
        'expected_result' => array(
            array(
                'source' => '/base/dir/area_one/fixture/theme_two/Fixture_ModuleOne',
                'destination' => 'area_one/fixture/theme_two/Fixture_ModuleOne',
            ),
            array(
                'source' => '/base/dir/area_one/fixture/theme_two/Fixture_ModuleTwo',
                'destination' => 'area_one/fixture/theme_two/Fixture_ModuleTwo',
            ),
            array(
                'source' => '/base/dir/area_one/fixture/theme_two',
                'destination' => 'area_one/fixture/theme_two',
            ),
        ),
    ),
    'fixture_three' => array(
        'theme' => $themeThree,
        'pattern_dir_map' => array(
            array('namespace' => '%namespace%', 'module' => '%module%', 'area' => 'area_two', 'theme' => $themeThree),
            array(
                '/base/dir/area_two/fixture/theme_three',
                '/base/dir/area_two/fixture/theme_three/%namespace%_%module%',
            ),
        ),
        'filesystem_glob_map' => array(
            '/base/dir/area_two/fixture/theme_three/', '*_*', array()
        ),
        'expected_result' => array(
            array(
                'source' => '/base/dir/area_two/fixture/theme_three',
                'destination' => 'area_two/fixture/theme_three',
            ),
        ),
    ),
);
