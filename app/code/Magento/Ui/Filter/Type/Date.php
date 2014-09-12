<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Filter\Type;

use Magento\Backend\Block\Context;
use Magento\Ui\Filter\FilterInterface;

/**
 * Class Date
 */
class Date implements FilterInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(Context $context, \Magento\Framework\Locale\ResolverInterface $localeResolver)
    {
        $this->localeDate = $context->getLocaleDate();
        $this->scopeConfig = $context->getScopeConfig();
        $this->localeResolver = $localeResolver;
    }

    /**
     * Get condition by data type
     *
     * @param string|array $value
     * @return array|null
     */
    public function getCondition($value)
    {
        return $this->convertValue($value);
    }

    /**
     * Convert value
     *
     * @param array|string|int|float $value
     * @return array|null
     */
    protected function convertValue($value)
    {
        if (!empty($value['from']) || !empty($value['to'])) {

            if (!empty($value['from'])) {
                $value['from'] = (int)$this->convertDate(strtotime($value['from']), $this->localeResolver->getLocale());
                $value['from'] = date('Y-m-d', $value['from']);
            }
            if (!empty($value['to'])) {
                $value['to'] = (int)$this->convertDate(strtotime($value['to']), $this->localeResolver->getLocale());
                $value['to'] = date('Y-m-d', $value['to']);
            }
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Convert given date to default (UTC) timezone
     *
     * @param string $date
     * @param string $locale
     * @return \Magento\Framework\Stdlib\DateTime\Date|null
     */
    protected function convertDate($date, $locale)
    {
        try {
            $dateObj = $this->localeDate->date(null, null, $locale, false);

            //set default timezone for store (admin)
            $dateObj->setTimezone(
                $this->scopeConfig->getValue(
                    $this->localeDate->getDefaultTimezonePath(),
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            //set beginning of day
            $dateObj->setHour(00);
            $dateObj->setMinute(00);
            $dateObj->setSecond(00);

            //set date with applying timezone of store
            $dateObj->set($date, null, $locale);

            //convert store date to default date in UTC timezone without DST
            $dateObj->setTimezone(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::DEFAULT_TIMEZONE);

            return $dateObj->getTimestamp();
        } catch (\Exception $e) {
            return null;
        }
    }
}
