<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$themeOne = new \Magento\Object(array(
    'area' => 'area_one',
    'theme_path' => 'fixture/theme_one',
));
$themeTwo = new \Magento\Object(array(
    'area' => 'area_one',
    'theme_path' => 'fixture/theme_two',
));
$themeThree = new \Magento\Object(array(
    'area' => 'area_two',
    'theme_path' => 'fixture/theme_three',
));
$themeFour = new \Magento\Object(array(
    'area' => 'area_two',
    'theme_path' => 'fixture/theme_four',
));

return array(
    'theme_customizing_one_module' => array(
        'theme' => $themeOne,
        'pattern_dir_map' => array(
            array(
                array('area' => 'area_one', 'theme' => $themeOne),
                array(
                    '/base/dir/area_one/fixture/theme_one',
                ),
            ),
            array(
                array('area' => 'area_one', 'theme' => $themeOne, 'namespace' => '%namespace%', 'module' => '%module%'),
                array(
                    '/base/dir/area_one/fixture/theme_one/%namespace%_%module%',
                ),
            ),
        ),
        'filesystem_glob_map' => array(
            array(
                '/base/dir/area_one/fixture/theme_one/', '*_*',
                array('/base/dir/area_one/fixture/theme_one/Magento_Core'),
            ),
        ),
        'expected_result' => array(
            array(
                'source' => '/base/dir/area_one/fixture/theme_one/Magento_Core',
                'destinationContext' => array(
                    'area' => 'area_one',
                    'themePath' => 'fixture/theme_one',
                    'locale' => null,
                    'module' => 'Magento_Core',
                ),
            ),
            array(
                'source' => '/base/dir/area_one/fixture/theme_one',
                'destinationContext' => array(
                    'area' => 'area_one',
                    'themePath' => 'fixture/theme_one',
                    'locale' => null,
                    'module' => null,
                ),
            ),
        ),
    ),
    'theme_customizing_two_modules' => array(
        'theme' => $themeTwo,
        'pattern_dir_map' => array(
            array(
                array('area' => 'area_one', 'theme' => $themeTwo),
                array(
                    '/base/dir/area_one/fixture/theme_two',
                ),
            ),
            array(
                array('area' => 'area_one', 'theme' => $themeTwo, 'namespace' => '%namespace%', 'module' => '%module%'),
                array(
                    '/base/dir/area_one/fixture/theme_two/%namespace%_%module%',
                ),
            ),
        ),
        'filesystem_glob_map' => array(
            array(
                '/base/dir/area_one/fixture/theme_two/', '*_*',
                array(
                    '/base/dir/area_one/fixture/theme_two/Fixture_ModuleOne',
                    '/base/dir/area_one/fixture/theme_two/Fixture_ModuleTwo',
                ),
            ),
        ),
        'expected_result' => array(
            array(
                'source' => '/base/dir/area_one/fixture/theme_two/Fixture_ModuleOne',
                'destinationContext' => array(
                    'area' => 'area_one',
                    'themePath' => 'fixture/theme_two',
                    'locale' => null,
                    'module' => 'Fixture_ModuleOne',
                ),
            ),
            array(
                'source' => '/base/dir/area_one/fixture/theme_two/Fixture_ModuleTwo',
                'destinationContext' => array(
                    'area' => 'area_one',
                    'themePath' => 'fixture/theme_two',
                    'locale' => null,
                    'module' => 'Fixture_ModuleTwo',
                ),
            ),
            array(
                'source' => '/base/dir/area_one/fixture/theme_two',
                'destinationContext' => array(
                    'area' => 'area_one',
                    'themePath' => 'fixture/theme_two',
                    'locale' => null,
                    'module' => null,
                ),
            ),
        ),
    ),
    'theme_customizing_no_modules' => array(
        'theme' => $themeThree,
        'pattern_dir_map' => array(
            array(
                array('area' => 'area_two', 'theme' => $themeThree),
                array(
                    '/base/dir/area_two/fixture/theme_three',
                ),
            ),
            array(
                array(
                    'area' => 'area_two', 'theme' => $themeThree, 'namespace' => '%namespace%', 'module' => '%module%'
                ),
                array(
                    '/base/dir/area_two/fixture/theme_three/%namespace%_%module%',
                ),
            ),
        ),
        'filesystem_glob_map' => array(
            array(
                '/base/dir/area_two/fixture/theme_three/', '*_*',
                array(),
            ),
        ),
        'expected_result' => array(
            array(
                'source' => '/base/dir/area_two/fixture/theme_three',
                'destinationContext' => array(
                    'area' => 'area_two',
                    'themePath' => 'fixture/theme_three',
                    'locale' => null,
                    'module' => null,
                )
            ),
        ),
    ),
    'fallback_pattern_mixing_slashes' => array(
        'theme' => $themeFour,
        'pattern_dir_map' => array(
            array(
                array('area' => 'area_two', 'theme' => $themeFour),
                array(
                    '/base/dir/area_two\\fixture\\theme_four',
                ),
            ),
            array(
                array(
                    'area' => 'area_two', 'theme' => $themeFour, 'namespace' => '%namespace%', 'module' => '%module%'
                ),
                array(
                    '/base/dir/area_two\\fixture\\theme_four\\%namespace%_%module%',
                ),
            ),
        ),
        'filesystem_glob_map' => array(
            array(
                '/base/dir/area_two/fixture/theme_four/', '*_*',
                array(),
            ),
        ),
        'expected_result' => array(
            array(
                'source' => '/base/dir/area_two/fixture/theme_four',
                'destinationContext' => array(
                    'area' => 'area_two',
                    'themePath' => 'fixture/theme_four',
                    'locale' => null,
                    'module' => null,
                )
            ),
        ),
    ),
);
