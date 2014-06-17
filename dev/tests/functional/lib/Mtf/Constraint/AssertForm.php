<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Constraint;

/**
 * Class AssertForm
 * Abstract class AssertForm
 */
abstract class AssertForm extends AbstractConstraint
{
    /**
     * Verify fixture and form data
     *
     * @param array $fixtureData
     * @param array $formData
     * @param bool $isStrict
     * @param bool $isPrepareError
     * @return array|string|null
     */
    protected function verifyData(array $fixtureData, array $formData, $isStrict = false, $isPrepareError = true)
    {
        $errors = [];
        $readyValues = [];

        foreach ($fixtureData as $key => $value) {
            $formValue = isset($formData[$key]) ? $formData[$key] : null;
            if (is_numeric($formValue)) {
                $formValue = floatval($formValue);
            }

            if (null === $formValue) {
                $errors[] = "- field \"{$key}\" is absent in form";
            } elseif (is_array($value)) {
                $valueErrors = $this->verifyData($value, $formValue, true, false);
                if ($valueErrors) {
                    $errors[$key] = $valueErrors;
                }
            } elseif ($value != $formValue) {
                if (is_array($formValue)) {
                    $formValue = $this->arrayToString($formValue);
                }
                $errors[] = "- {$key}: \"{$formValue}\" instead of \"{$value}\"";
            }

            $readyValues[] = $key;
        }

        $diffData = $isStrict ? array_diff(array_keys($formData), $readyValues) : null;
        if ($diffData) {
            $errors[] = '- fields ' . implode(', ', $diffData) . ' is absent in fixture';
        }

        if (empty($errors)) {
            return null;
        }
        return $isPrepareError ? $this->prepareErrors($errors) : $errors;
    }

    /**
     * Prepare errors to string
     *
     * @param array $errors
     * @param string|null $notice
     * @param string $indent
     * @return string
     */
    protected function prepareErrors(array $errors, $notice = null, $indent = '')
    {
        $result = [];

        foreach ($errors as $key => $error) {
            $result[] = is_array($error)
                ? $this->prepareErrors($error, "{$indent}{$key}:\n", $indent . "\t")
                : ($indent . $error);
        }

        if (null === $notice) {
            $notice = "\nForm data not equals to passed from fixture:\n";
        }
        return $notice . implode("\n", $result);
    }

    /**
     * Convert array to string
     *
     * @param array $array
     * @return string
     */
    private function arrayToString(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $value = is_array($value) ? $this->arrayToString($value) : $value;
            $result[] = "{$key} => {$value}";
        }

        return '[' . implode(', ', $result) . ']';
    }
}
