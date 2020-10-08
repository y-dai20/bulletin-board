<?php

class Validator
{
    protected $rules;

    public function __construct($rules)
    {
        $this->rules = $rules;
    }

    public function validate($data)
    {
        $errors    = [];
        $className = get_class($this);

        foreach ($this->rules as $inputName => $rule) {
            foreach ($rule as $method => $value) {
                if (!method_exists($className, $method)) {
                    throw new Exception("{$className}::{$method}() isn't existed.");
                }

                if (array_key_exists($inputName, $data)) {
                    $error = call_user_func_array([$className, $method], [$data[$inputName], $inputName, $value]);

                    if (!is_empty($error)) {
                        $errors[] = $error;
                    }
                }
            }
        }

        return $errors;
    }

    protected function required($input, $key, $required)
    {
        if (is_empty($input) && $required) {
            return  $key . ' is empty.';
        }

        return '';
    }

    protected function numbers($input, $key, $bool)
    {
        if (!$bool || is_empty($input)) {
            return '';
        }

        if (!ctype_digit($input)) {
            return "Your {$key} must be all digit numbers.";
        }

        return '';
    }

    protected function length($input, $key, $len)
    {
        if (is_empty($input)) {
            return '';
        }

        if (mb_strlen($input) !== $len) {
            return "Your {$key} must be {$len} characters long.";
        }

        return '';
    }

    protected function minLength($input, $key, $min)
    {
        if (is_empty($input)) {
            return '';
        }

        if (mb_strlen($input) < $min) {
            return "Your {$key} must be {$min} characters or more.";
        }

        return '';
    }

    protected function maxLength($input, $key, $max)
    {
        if (is_empty($input)) {
            return '';
        }

        if (mb_strlen($input) > $max) {
            return "Your {$key} must be {$max} characters or less.";
        }

        return '';
    }

    protected function between($input, $key, $between)
    {
        if (is_empty($input)) {
            return '';
        }

        if (!array_isset('min', $between) || !array_isset('max', $between)) {
            throw new Exception(__METHOD__ . "() min or max isn't setted.");
        }

        $len = mb_strlen($input);

        if ($len < $between['min'] || $len > $between['max']) {
            return "Your {$key} must be {$between['max']} to {$between['min']} characters long.";
        }

        return '';
    }

    protected function email($input, $key, $bool)
    {
        if (!$bool || is_empty($input)) {
            return '';
        }
        if (filter_var($input, FILTER_VALIDATE_EMAIL) === false) {
            return  "Your {$key} is incorrect format.";
        }

        return '';
    }

    protected function mimetypes($input, $key, $allowedTypes)
    {
        if (is_empty($input)) {
            return '';
        }

        if (is_empty($this->file($input, $key))) {
            $mimetype = explode('/', mime_content_type($input['tmp_name']));

            if (!in_array($mimetype[1], $allowedTypes)) {
                $types = implode(', ', $allowedTypes);
                return "Invalid image type. {$types} only.";
            }
        }

        return '';
    }

    protected function maxFileSize($input, $key, $maxFileSize)
    {
        if (is_empty($input)) {
            return '';
        }

        if (is_empty($this->file($input, $key))) {
            if (!array_isset('size', $input)) {
                throw new Exception(__METHOD__ . "() Can't find filesize.");
            }

            if ($input['size'] > $maxFileSize) {
                $formattedSizeUnit = format_size_unit($maxFileSize);

                return "Your {$key} is only valid {$formattedSizeUnit} or less";
            }
        }

        return '';
    }

    protected function file($input, $key, $bool = true)
    {
        if (!$bool || is_empty($input)) {
            return '';
        }

        if (!array_isset('tmp_name', $input) || !is_uploaded_file($input['tmp_name'])) {
            throw new Exception(__METHOD__ . "() {$key} isn't file.");
        }

        return '';
    }
}
