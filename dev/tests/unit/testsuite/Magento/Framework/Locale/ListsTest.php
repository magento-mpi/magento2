<?php
/**
 * Created by PhpStorm.
 * User: rbates
 * Date: 6/25/14
 * Time: 11:34 AM
 */

namespace Magento\Framework\Locale;

class ListsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Locale\Lists
     */
    protected $listsModel;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\App\State
     */
    protected $mockAppState;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\App\ScopeResolverInterface
     */
    protected $mockScopeResolver;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Locale\ConfigInterface
     */
    protected $mockConfig;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Locale\ResolverInterface
     */
    protected $mockLocaleResolver;

    protected function setUp()
    {
        $this->mockAppState = $this->getMockBuilder('\Magento\Framework\App\State')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockScopeResolver = $this->getMockBuilder('\Magento\Framework\App\ScopeResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockConfig = $this->getMockBuilder('\Magento\Framework\Locale\ConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockLocaleResolver = $this->getMockBuilder('\Magento\Framework\Locale\ResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $locale = "some_locale";
        $this->mockLocaleResolver->expects($this->atLeastOnce())
            ->method('setLocale')
            ->with($locale);

        $this->listsModel = new \Magento\Framework\Locale\Lists(
            $this->mockAppState,
            $this->mockScopeResolver,
            $this->mockConfig,
            $this->mockLocaleResolver,
            $locale
        );
    }

    public function testGetCountryTranslationList()
    {
        $mockLocale = $this->getMockBuilder('\Magento\Framework\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockLocaleResolver->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue($mockLocale));

        $someArray = [2, 7, 1, 8];

        $mockLocale->staticExpects($this->once())
            ->method('getTranslationList')
            ->with('territory', $mockLocale, 2)
            ->will($this->returnValue($someArray));

        $this->assertSame($someArray, $this->listsModel->getCountryTranslationList());
    }

    public function testGetCountryTranslation()
    {
        $mockLocale = $this->getMockBuilder('\Magento\Framework\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockLocaleResolver->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue($mockLocale));

        $countyTranslation = 'some string';
        $value = 'some value';

        $mockLocale->staticExpects($this->once())
            ->method('getTranslation')
            ->with($value, 'country', $mockLocale)
            ->will($this->returnValue($countyTranslation));

        $this->assertSame($countyTranslation, $this->listsModel->getCountryTranslation($value));
    }

    public function testGetTranslationList()
    {
        $mockLocale = $this->getMockBuilder('\Magento\Framework\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockLocaleResolver->expects($this->exactly(2))
            ->method('getLocale')
            ->will($this->returnValue($mockLocale));

        $someArray = [3, 1, 4, 5];
        $path = 'path';
        $value = 'value';

        $mockLocale->staticExpects($this->once())
            ->method('getTranslationList')
            ->with($path, $mockLocale, $value)
            ->will($this->returnValue($someArray));

        $this->assertSame($someArray, $this->listsModel->getTranslationList($path, $value));
    }

    public function testGetOptionAllCurrencies()
    {
        $mockLocale = $this->getMockBuilder('\Magento\Framework\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockLocaleResolver->expects($this->exactly(2))
            ->method('getLocale')
            ->will($this->returnValue($mockLocale));

        $someArray = [
            'A label' => 'A value',
            'B label' => 'B value',
            'C label' => 'C value'
        ];
        $path = 'currencytoname';

        $mockLocale->staticExpects($this->once())
            ->method('getTranslationList')
            ->with($path, $mockLocale, null)
            ->will($this->returnValue($someArray));

        $expectedArray = [
            ['value' => 'A value', 'label' => 'A label'],
            ['value' => 'B value', 'label' => 'B label'],
            ['value' => 'C value', 'label' => 'C label'],
        ];

        $this->assertSame($expectedArray, $this->listsModel->getOptionAllCurrencies());
    }

    public function testGetOptionCurrencies()
    {
        $mockLocale = $this->getMockBuilder('\Magento\Framework\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockLocaleResolver->expects($this->exactly(2))
            ->method('getLocale')
            ->will($this->returnValue($mockLocale));

        $this->mockAppState->expects($this->once())
            ->method('isInstalled')
            ->will($this->returnValue(false));

        $allowedCurrencies = ['USD', 'GBP', 'EUR'];

        $this->mockConfig->expects($this->once())
            ->method('getAllowedCurrencies')
            ->will($this->returnValue($allowedCurrencies));

        $someArray = [
            'United Stated Dollar' => 'USD',
            'Pound Sterling'       => 'GBP',
            'Swiss Frank'          => 'CHF',
        ];
        $path = 'currencytoname';

        $mockLocale->staticExpects($this->once())
            ->method('getTranslationList')
            ->with($path, $mockLocale, null)
            ->will($this->returnValue($someArray));

        $expectedArray = [
            ['value' => 'GBP', 'label' => 'Pound Sterling'],
            ['value' => 'USD', 'label' => 'United Stated Dollar'],
        ];

        $this->assertSame($expectedArray, $this->listsModel->getOptionCurrencies());
    }

    public function testGetOptionCountries()
    {
        $mockLocale = $this->getMockBuilder('\Magento\Framework\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockLocaleResolver->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue($mockLocale));

        // TODO:  Is this correct?  Is it expecting the value => label
        $someArray = [
            'A value' => 'A label',
            'B value' => 'B label',
            'C value' => 'C label'
        ];

        $mockLocale->staticExpects($this->once())
            ->method('getTranslationList')
            ->with('territory', $mockLocale, 2)
            ->will($this->returnValue($someArray));

        $expectedArray = [
            ['value' => 'A value', 'label' => 'A label'],
            ['value' => 'B value', 'label' => 'B label'],
            ['value' => 'C value', 'label' => 'C label'],
        ];

        $this->assertEquals($expectedArray, $this->listsModel->getOptionCountries());
    }

    public function testGetOptionsWeekdays()
    {
        $mockLocale = $this->getMockBuilder('\Magento\Framework\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockLocaleResolver->expects($this->exactly(2))
            ->method('getLocale')
            ->will($this->returnValue($mockLocale));

        $someArray = [
            'format' => [
                'wide' => [
                    'A value' => 'A label',
                    'B value' => 'B label',
                    'C value' => 'C label'
                ]
            ]
        ];
        $path = 'days';

        $mockLocale->staticExpects($this->once())
            ->method('getTranslationList')
            ->with($path, $mockLocale, null)
            ->will($this->returnValue($someArray));

        $expectedArray = [
            ['value' => 'A value', 'label' => 'A label'],
            ['value' => 'B value', 'label' => 'B label'],
            ['value' => 'C value', 'label' => 'C label'],
        ];

        $this->assertEquals($expectedArray, $this->listsModel->getOptionWeekdays(true, true));
    }

    public function testGetOptionTimezones()
    {
        $mockLocale = $this->getMockBuilder('\Magento\Framework\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockLocaleResolver->expects($this->exactly(2))
            ->method('getLocale')
            ->will($this->returnValue($mockLocale));

        $someArray = [
            'A value' => 'A label',
            'B value' => 'B label',
            'C value' => 'C label'
        ];
        $path = 'windowstotimezone';

        $mockLocale->staticExpects($this->once())
            ->method('getTranslationList')
            ->with($path, $mockLocale, null)
            ->will($this->returnValue($someArray));

        $expectedArray = [
            ['value' => 'A value', 'label' => 'A label (A value)'],
            ['value' => 'B value', 'label' => 'B label (B value)'],
            ['value' => 'C value', 'label' => 'C label (C value)'],
        ];

        $this->assertEquals($expectedArray, $this->listsModel->getOptionTimezones());
    }

    public function testGetOptionLocales()
    {
        $this->setupForOptionLocales();

        $this->assertEquals(
            [['value' => 'en_US', 'label' => 'English (United States)']],
            $this->listsModel->getOptionLocales()
        );
    }

    public function testGetTranslatedOptionLocales()
    {
        $mockLocale = $this->setupForOptionLocales();

        $mockLocale->staticExpects($this->any())
            ->method('getTranslation')
            ->will(
                $this->returnValueMap(
                    [
                        ['en', 'language', 'en_US', 'English'],
                        ['US', 'country', 'en_US', 'United States of America']
                    ]
                )
            );

        $this->assertEquals(
            [['value' => 'en_US', 'label' => 'English (United States of America) / English (United States)']],
            $this->listsModel->getTranslatedOptionLocales()
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\LocaleInterface
     */
    protected function setupForOptionLocales()
    {
        $mockLocale = $this->getMockBuilder('\Magento\Framework\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockLocaleResolver->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($mockLocale));

        $localeList = ['en_US' => 'US English', 'unallowed_locale' => 'not allowed'];
        $mockLocale->staticExpects($this->once())
            ->method('getLocaleList')
            ->will($this->returnValue($localeList));

        $languageList = ['en' => 'English'];
        $countryList = ['US' => 'United States'];
        $mockLocale->staticExpects($this->any())
            ->method('getTranslationList')
            ->will(
                $this->returnValueMap(
                    [
                        ['language', $mockLocale, null, $languageList],
                        ['territory', $mockLocale, 2, $countryList]
                    ]
                )
            );

        $allowedLocales = ['en_US'];
        $this->mockConfig->expects($this->once())
            ->method('getAllowedLocales')
            ->will($this->returnValue($allowedLocales));

        return $mockLocale;
    }
}
