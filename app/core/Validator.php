<?php
declare(strict_types=1);

class Validator
{
    public static function validate(array $data, array $rules)
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = array_key_exists($field, $data) ? trim((string) $data[$field]) : '';
            $rulesList = is_array($ruleSet) ? $ruleSet : explode('|', (string) $ruleSet);

            $isRequired = in_array('required', $rulesList, true);
            $isNullable = in_array('nullable', $rulesList, true);

            if ($isRequired && $value === '') {
                $errors[$field] = self::label($field) . ' is required.';
                continue;
            }

            if ($value === '' && !$isRequired && $isNullable) {
                continue;
            }

            if ($value === '' && !$isRequired) {
                continue;
            }

            foreach ($rulesList as $rule) {
                if ($rule === 'required' || $rule === 'nullable') {
                    continue;
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = self::label($field) . ' must be a valid email address.';
                    break;
                }

                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = self::label($field) . ' must be at least ' . $min . ' characters.';
                        break;
                    }
                }

                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = self::label($field) . ' must not exceed ' . $max . ' characters.';
                        break;
                    }
                }

                if (strpos($rule, 'same:') === 0) {
                    $other = substr($rule, 5);
                    $otherValue = array_key_exists($other, $data) ? (string) $data[$other] : '';
                    if ((string) $value !== $otherValue) {
                        $errors[$field] = self::label($field) . ' must match ' . self::label($other) . '.';
                        break;
                    }
                }

                if ($rule === 'confirmed') {
                    $confirmationField = $field . '_confirmation';
                    $confirmationValue = array_key_exists($confirmationField, $data) ? (string) $data[$confirmationField] : '';
                    if ((string) $value !== $confirmationValue) {
                        $errors[$field] = self::label($field) . ' confirmation does not match.';
                        break;
                    }
                }

                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field] = self::label($field) . ' must be a number.';
                    break;
                }

                if ($rule === 'integer' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $errors[$field] = self::label($field) . ' must be an integer.';
                    break;
                }

                if ($rule === 'boolean' && !in_array($value, ['0', '1', 0, 1, true, false, 'true', 'false', 'on', 'off'], true)) {
                    $errors[$field] = self::label($field) . ' must be true or false.';
                    break;
                }

                if ($rule === 'date' && strtotime($value) === false) {
                    $errors[$field] = self::label($field) . ' must be a valid date.';
                    break;
                }

                if (strpos($rule, 'in:') === 0) {
                    $allowed = explode(',', substr($rule, 3));
                    if (!in_array($value, $allowed, true)) {
                        $errors[$field] = self::label($field) . ' has an invalid value.';
                        break;
                    }
                }

                if (strpos($rule, 'regex:') === 0) {
                    $pattern = substr($rule, 6);
                    if (!preg_match($pattern, $value)) {
                        $errors[$field] = self::label($field) . ' format is invalid.';
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    public static function firstError(array $errors)
    {
        if (empty($errors)) {
            return null;
        }

        return reset($errors);
    }

    private static function label($field)
    {
        return ucwords(str_replace(['_', '.'], ' ', $field));
    }
}