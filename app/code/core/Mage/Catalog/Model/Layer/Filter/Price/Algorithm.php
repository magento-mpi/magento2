<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Algorithm for layer price filter
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Layer_Filter_Price_Algorithm
{
    const MIN_POSSIBLE_PRICE = .01;
    const TEN_POWER_ROUNDING_FACTOR = 4;
    const INTERVAL_DEFLECTION_LIMIT = .3;

    /**
     * Sorted array of all products prices
     *
     * @var array
     */
    protected $_prices = null;

    /**
     * Number of segmentation intervals
     *
     * @var null|int
     */
    protected $_intervalsNumber = null;

    /**
     * Upper limits of skipped quantiles
     *
     * @var array
     */
    protected $_skippedQuantilesUpperLimits = array();

    /**
     * Upper prices limit
     *
     * @var null|float
     */
    protected $_upperLimit = null;

    /**
     * Lower prices limit
     *
     * @var null|float
     */
    protected $_lowerLimit = null;

    /**
     * Check prices to be in limits interval
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price_Algorithm
     */
    protected function _checkPrices()
    {
        if (is_null($this->_prices)) {
            return $this;
        }
        if (!is_null($this->_upperLimit) || !is_null($this->_lowerLimit)) {
            foreach ($this->_prices as $i => $price) {
                if (!is_null($this->_lowerLimit) && $price < $this->_lowerLimit
                    || !is_null($this->_upperLimit) && $price >= $this->_upperLimit
                ) {
                    unset($this->_prices[$i]);
                }
            }
        }
        sort($this->_prices);
        return $this;
    }

    /**
     * Set lower and upper limit for algorithm
     *
     * @param null|float $lowerLimit
     * @param null|float $upperLimit
     * @return Mage_Catalog_Model_Layer_Filter_Price_Algorithm
     */
    public function setLimits($lowerLimit = null, $upperLimit = null)
    {
        $this->_lowerLimit = empty($lowerLimit) ? null : (float)$lowerLimit;
        $this->_upperLimit = empty($upperLimit) ? null : (float)$upperLimit;
        $this->_checkPrices();
        return $this;
    }

    /**
     * Set products prices
     *
     * @param array $prices
     * @return Mage_Catalog_Model_Layer_Filter_Price_Algorithm
     */
    public function setPrices(array $prices)
    {
        $this->_prices = $prices;
        $this->_checkPrices();
        $this->_intervalsNumber = null;
        $this->_skippedQuantilesUpperLimits = array();

        return $this;
    }

    /**
     * Get min price
     *
     * @return float
     */
    public function getMinPrice()
    {
        return empty($this->_prices) ? 0 : $this->_prices[0];
    }

    /**
     * Get max price
     *
     * @return float
     */
    public function getMaxPrice()
    {
        return (empty($this->_prices)) ? 0 : $this->_prices[count($this->_prices) - 1];
    }

    /**
     * Get amount of segmentation intervals
     *
     * @return int
     */
    public function getIntervalsNumber()
    {
        if (!is_null($this->_intervalsNumber)) {
            return $this->_intervalsNumber;
        }

        $pricesCount = count($this->_prices);
        if ($pricesCount < 2 || ($this->getMaxPrice() - $this->getMinPrice() == 0)) {
            //Same price couldn't be separated with several intervals
            $this->_intervalsNumber = 1;
            return $this->_intervalsNumber;
        }

        $sum = 0;
        $sumSquares = 0;
        foreach ($this->_prices as $price) {
            $sum += $price;
            $sumSquares += $price * $price;
        }

        if ($pricesCount * $sumSquares - $sum * $sum <= 0) {
            $intervalsNumber = 1000;
        } else {
            $intervalsNumber = ($this->getMaxPrice() - $this->getMinPrice()) * pow($pricesCount, 5 / 6)
                * sqrt(($pricesCount - 1) / ($pricesCount * $sumSquares - $sum * $sum)) / 3.5;
        }
        $this->_intervalsNumber = min(max(ceil($intervalsNumber), 2), 10);

        return $this->_intervalsNumber;
    }

    /**
     * Get quantile
     *
     * @param int $quantileNumber should be from 1 to n-1 where n is number of intervals
     * @return float|null
     */
    protected function _getQuantile($quantileNumber)
    {
        if ($quantileNumber < 1 || $quantileNumber >= $this->getIntervalsNumber()) {
            return 0;
        }

        return $quantileNumber * count($this->_prices) / $this->getIntervalsNumber() - .5;
    }

    /**
     * Get quantile interval
     *
     * @param int $quantileNumber should be from 1 to n-1 where n is number of intervals
     * @return null|array [floatMin,floatMax]
     */
    protected function _getQuantileInterval($quantileNumber)
    {
        if ($quantileNumber < 1 || $quantileNumber >= $this->getIntervalsNumber()) {
            return null;
        }
        $pricesCount = count($this->_prices);
        $quantile = $this->_getQuantile($quantileNumber);
        $deflectionLimit = floor($pricesCount / 2 / $this->getIntervalsNumber());
        $limits = array(
            min(floor($quantile - $deflectionLimit), floor($quantile)),
            max(ceil($quantile + $deflectionLimit - 1), ceil($quantile)),
        );

        $deflection = $this->_getStandardNormalDistribution()
            * sqrt($pricesCount * $quantileNumber * ($this->getIntervalsNumber() - $quantileNumber))
            / $this->getIntervalsNumber();
        $left = max(floor($quantile - $deflection - 1), $limits[0], 0);
        if (array_key_exists($quantileNumber - 1, $this->_skippedQuantilesUpperLimits)
            && $left > $this->_skippedQuantilesUpperLimits[$quantileNumber - 1]
        ) {
            $left = $this->_skippedQuantilesUpperLimits[$quantileNumber - 1];
        }
        $right = min(ceil($quantile + $deflection), $limits[1], $pricesCount - 1);
        return array($left, $right);
     }

    /**
     * Get standard normal distribution
     *
     * @return float
     */
    protected function _getStandardNormalDistribution()
    {
        return 1.96;
    }

    /**
     * Find max rounding factor with given price range
     *
     * @param float $lowerPrice
     * @param float $upperPrice
     * @param bool $returnEmpty whether empty result is acceptable
     * @param null|float $roundingFactor if given, checks for range to contain the factor
     * @return false|array
     */
    protected function _findRoundPrice($lowerPrice, $upperPrice, $returnEmpty = true, $roundingFactor = null)
    {
        $lowerPrice = round($lowerPrice, 3);
        $upperPrice = round($upperPrice, 3);

        if (!is_null($roundingFactor)) {
            // Can't separate if prices are equal
            if ($lowerPrice >= $upperPrice) {
                if ($lowerPrice > $upperPrice || $returnEmpty) {
                    return false;
                }
            }
            // round is used for such examples: (1194.32 / 0.02) or (5 / 100000)
            $lowerDivision = ceil(round($lowerPrice / $roundingFactor, self::TEN_POWER_ROUNDING_FACTOR + 3));
            $upperDivision = floor(round($upperPrice / $roundingFactor, self::TEN_POWER_ROUNDING_FACTOR + 3));
            if ($lowerDivision > $upperDivision) {
                return false;
            }
            $averageDivision = ($lowerDivision + $upperDivision) / 2;
            $lowerAverageDivision = floor($averageDivision);
            $result = array(round($lowerAverageDivision * $roundingFactor, 2));
            if ($averageDivision != $lowerAverageDivision) {
                $upperAverageDivision = ceil($averageDivision);
                $result[] = round($upperAverageDivision * $roundingFactor, 2);
            }
            return $result;
        }

        $result = array();
        $tenPower = pow(10, self::TEN_POWER_ROUNDING_FACTOR);
        $roundingFactorCoefficients = array(10, 5, 2);
        while ($tenPower >= self::MIN_POSSIBLE_PRICE) {
            if ($tenPower == self::MIN_POSSIBLE_PRICE) {
                $roundingFactorCoefficients[] = 1;
            }
            foreach ($roundingFactorCoefficients as $roundingFactorCoefficient) {
                $roundingFactorCoefficient *= $tenPower;
                $roundPrices = $this->_findRoundPrice(
                    $lowerPrice, $upperPrice, $returnEmpty, $roundingFactorCoefficient
                );
                if ($roundPrices) {
                    $result[round($roundingFactorCoefficient / self::MIN_POSSIBLE_PRICE)] = $roundPrices;
                }
            }
            $tenPower /= 10;
        }

        return empty($result) ? array(1 => array()) : $result;
    }

    /**
     * Merge new round prices with old ones
     *
     * @param array $oldRoundPrices
     * @param array $newRoundPrices
     * @return void
     */
    protected function _mergeRoundPrices(&$oldRoundPrices, &$newRoundPrices)
    {
        foreach ($newRoundPrices as $roundingFactor => $roundPriceValues) {
            if (array_key_exists($roundingFactor, $oldRoundPrices)) {
                $oldRoundPrices[$roundingFactor] = array_unique(array_merge(
                    $oldRoundPrices[$roundingFactor],
                    $roundPriceValues
                ));
            } else {
                $oldRoundPrices[$roundingFactor] = $roundPriceValues;
            }
        }
    }

    /**
     * Find price separator for the quantile
     *
     * @param int $quantileNumber should be from 1 to n-1 where n is number of intervals
     * @return array|null
     */
    protected function _findPriceSeparator($quantileNumber)
    {
        if ($quantileNumber < 1 || $quantileNumber >= $this->getIntervalsNumber()) {
            return null;
        }
        $quantile = $this->_getQuantile($quantileNumber);
        $lowerQuantile = floor($quantile);
        $upperQuantile = ceil($quantile);

        $quantileInterval = $this->_getQuantileInterval($quantileNumber);
        $quantileDeflection = 0;
        $bestRoundPrice = array();

        if ($this->_prices[$quantileInterval[0]] == $this->_prices[$quantileInterval[1]]) {
            if ($quantileNumber == 1) {
                $i = $quantileInterval[0];
                while ($i >= 0 && ($this->_prices[$i] == $this->_prices[$quantileInterval[1]])) {
                    --$i;
                }
                if ($i >= 0) {
                    $bestRoundPrice = $this->_findRoundPrice(
                        $this->_prices[$i] + self::MIN_POSSIBLE_PRICE / 10,
                        $this->_prices[$quantileInterval[1]],
                        false
                    );
                }
            }
            if ($quantileNumber == $this->getIntervalsNumber() - 1) {
                $pricesCount = count($this->_prices);
                $i = $quantileInterval[1];
                while ($i < $pricesCount && ($this->_prices[$quantileInterval[0]] == $this->_prices[$i])) {
                    ++$i;
                }
                if ($i < $pricesCount) {
                    $upperBestRoundPrice = $this->_findRoundPrice(
                        $this->_prices[$quantileInterval[0]] + self::MIN_POSSIBLE_PRICE / 10,
                        $this->_prices[$i],
                        false
                    );
                    $this->_mergeRoundPrices($bestRoundPrice, $upperBestRoundPrice);
                }
            }
        } else {
            while ($lowerQuantile - $quantileDeflection >= $quantileInterval[0]
                || $upperQuantile + $quantileDeflection <= $quantileInterval[1]
            ) {
                $leftIndex = max($quantileInterval[0], $lowerQuantile - $quantileDeflection);
                $rightIndex = min($quantileInterval[1], $upperQuantile + $quantileDeflection);

                $roundPrices = $this->_findRoundPrice(
                    $this->_prices[$leftIndex] + self::MIN_POSSIBLE_PRICE / 10,
                    $this->_prices[$rightIndex]
                );
                $this->_mergeRoundPrices($bestRoundPrice, $roundPrices);
                ++$quantileDeflection;
            }
        }

        if (empty($bestRoundPrice)) {
            $this->_skippedQuantilesUpperLimits[$quantileNumber] = $quantileInterval[1];
            return $bestRoundPrice;
        }

        ksort($bestRoundPrice, SORT_NUMERIC);
        foreach ($bestRoundPrice as $index => &$bestRoundPriceValues) {
            if (empty($bestRoundPriceValues)) {
                unset($bestRoundPrice[$index]);
            } else {
                sort($bestRoundPriceValues);
            }
        }
        return array_reverse($bestRoundPrice);
    }

    /**
     * Get separator nearest to quantile among the separators
     *
     * @param int $quantileNumber
     * @param array $separators
     * @param int $priceIndex
     * @return bool|array [separatorPrice, pricesCount]
     */
    protected function _findBestSeparator($quantileNumber, $separators, $priceIndex)
    {
        $result = false;

        $i = $priceIndex;
        $pricesCount = count($this->_prices);
        while ($i < $pricesCount && !empty($separators)) {
            if ($this->_prices[$i] < $separators[0]) {
                ++$i;
            } else {
                $separator = array_shift($separators);

                $deflection = abs(($quantileNumber + 1) * $pricesCount - $i * $this->getIntervalsNumber());
                if (!$result || $deflection < $result[0]) {
                    $result = array($deflection, $separator, $i - $priceIndex);
                }
            }
        }

        return $result ? $result : false;
    }

    /**
     * Calculate separators, each contains 'from', 'to' and 'count'
     *
     * @return array
     */
    public function calculateSeparators()
    {
        $this->_checkPrices();
        $separators = array();
        for ($i = 1; $i < $this->getIntervalsNumber(); ++$i) {
            $separators[] = $this->_findPriceSeparator($i);
        }
        $pricesCount = count($this->_prices);

        $i = 0;
        $result = array();
        $lastSeparator = is_null($this->_lowerLimit) ? 0 : $this->_lowerLimit;
        $quantile = 0;
        $pricesPerInterval = $pricesCount / $this->getIntervalsNumber();
        while (!empty($separators) && ($i < $pricesCount)) {
            while (!empty($separators) && empty($separators[0])) {
                array_shift($separators);
            }
            if (empty($separators)) {
                break;
            }

            $separatorCandidate = false;
            $newLastSeparator = $lastSeparator;
            while (!empty($separators[0]) && !array_key_exists($quantile, $result)) {
                $separatorsPortion = array_shift($separators[0]);
                $bestSeparator = $this->_findBestSeparator($quantile, $separatorsPortion, $i);
                if ($bestSeparator && $bestSeparator[2] > 0) {
                    $isEqualPrice = ($this->_prices[$i] == $this->_prices[$i + $bestSeparator[2] - 1])
                        ? $this->_prices[$i]
                        : false;
                    $separatorData = array(
                        'from'  => ($isEqualPrice !== false) ? $isEqualPrice : $lastSeparator,
                        'to'    => ($isEqualPrice !== false) ? $isEqualPrice : $bestSeparator[1],
                        'count' => $bestSeparator[2],
                    );
                    if (abs(1 - $bestSeparator[2] / $pricesPerInterval) <= self::INTERVAL_DEFLECTION_LIMIT) {
                        $newLastSeparator = $bestSeparator[1];
                        $result[$quantile] = $separatorData;
                    } elseif (!$separatorCandidate || $bestSeparator[0] < $separatorCandidate[0]) {
                        $separatorCandidate = array($bestSeparator[0], $separatorData, $bestSeparator[1]);
                    }
                }
            }

            if (!array_key_exists($quantile, $result) && $separatorCandidate) {
                $newLastSeparator = $separatorCandidate[2];
                $result[$quantile] = $separatorCandidate[1];
            }

            if (array_key_exists($quantile, $result)) {
                $lastSeparator = $newLastSeparator;
                while ($i < $pricesCount && $this->_prices[$i] < $lastSeparator) {
                    ++$i;
                }
                array_shift($separators);
            }
            ++$quantile;
        }
        if ($i < $pricesCount) {
            $isEqualPrice = ($this->_prices[$i] == $this->_prices[$pricesCount - 1]) ? $this->_prices[$i] : false;
            $result[$quantile] = array(
                'from'  => $isEqualPrice ? $isEqualPrice : $lastSeparator,
                'to'    => $isEqualPrice ? $isEqualPrice : (is_null($this->_upperLimit) ? '' : $this->_upperLimit),
                'count' => $pricesCount - $i,
            );
        }

        return array_values($result);
    }
}
