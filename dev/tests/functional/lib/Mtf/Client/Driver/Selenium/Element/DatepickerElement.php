<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element as ElementInterface;

/**
 * Class DatepickerElement
 * General class for datepicker elements.
 */
class DatepickerElement extends Element
{
    /**
     * DatePicker button
     *
     * @var string
     */
    protected $datePickerButton = 'ui-datepicker-trigger';

    /**
     * DatePicker block
     *
     * @var string
     */
    protected $datePickerBlock = 'ui-datepicker-div';

    /**
     * Field Month on the DatePicker
     *
     * @var string
     */
    protected $datePickerMonth = 'ui-datepicker-month';

    /**
     * Field Year on the DatePicker
     *
     * @var string
     */
    protected $datePickerYear = 'ui-datepicker-year';

    /**
     * Calendar on the DatePicker
     *
     * @var string
     */
    protected $datePickerCalendar = 'ui-datepicker-calendar';

    /**
     * DatePicker button 'Close'
     *
     * @var string
     */
    protected $datePickerButtonClose = 'ui-datepicker-close';

    /**
     * Set the date from datePicker
     *
     * @param array|string $value
     */
    public function setValue($value)
    {
        $date = $this->parseDate($value);

        $this->find(
            './/following-sibling::img[contains(@class,"' . $this->datePickerButton . '")]',
            ElementInterface\Locator::SELECTOR_XPATH
        )->click();

        $datapicker = $this->find(
            './/ancestor::body//*[contains(@id,"' . $this->datePickerBlock . '")]',
            ElementInterface\Locator::SELECTOR_XPATH
        );

        $datapicker->find(
            './/*[contains(@class,"' . $this->datePickerMonth . '")]',
            ElementInterface\Locator::SELECTOR_XPATH,
            'select'
        )->setValue($date[0]);

        $datapicker->find(
            './/*[contains(@class,"' . $this->datePickerYear . '")]',
            ElementInterface\Locator::SELECTOR_XPATH,
            'select'
        )->setValue($date[2]);

        $datapicker->find(
            './/*[contains(@class,"' . $this->datePickerCalendar . '")]//*/td/a[text()=' . $date[1] . ']',
            ElementInterface\Locator::SELECTOR_XPATH
        )->click();

        $datapicker->find(
            './/*[contains(@class,"' . $this->datePickerButtonClose . '")]',
            ElementInterface\Locator::SELECTOR_XPATH
        )->click();
    }

    /**
     * Get value
     *
     * @return string|void
     */
    public function getValue()
    {
        $this->getValue();
    }

    /**
     * Parse date from string to array
     * @param $value
     *
     * @return array
     */
    protected function parseDate($value)
    {
        $date = strtotime($value);
        $date = strftime("%b %#d, %Y %I:%M %p", $date);
        $date = preg_split('/[,\s]/', $date);
        array_splice($date, 2, 1);

        return $date;
    }
}
