<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Form_validation {
    private $rules = [];
    private $errors = [];
    private $data;
    private $current_field;
    private $messages = [];

    public function __construct() {
        $this->data = array_merge($_POST, $_GET);
    }

    public function name($name) {
        $this->current_field = $name;
        if (!isset($this->rules[$this->current_field])) {
            $this->rules[$this->current_field] = [];
            $this->messages[$this->current_field] = [];
        }
        return $this;
    }

    public function required($custom_error = null) {
        $this->rules[$this->current_field]['required'] = true;
        $this->messages[$this->current_field]['required'] = $custom_error ?: "The {$this->current_field} field is required.";
        return $this;
    }

    public function matches($field, $custom_error = null) {
        $this->rules[$this->current_field]['matches'] = $field;
        $this->messages[$this->current_field]['matches'] = $custom_error ?: "The {$this->current_field} field does not match the {$field} field.";
        return $this;
    }

    public function is_unique($table, $field, $custom_error = null) {
        $this->rules[$this->current_field]['is_unique'] = [$table, $field];
        $this->messages[$this->current_field]['is_unique'] = $custom_error ?: "The {$this->current_field} field must contain a unique value.";
        return $this;
    }

    public function min_length($length, $custom_error = null) {
        $this->rules[$this->current_field]['min_length'] = $length;
        $this->messages[$this->current_field]['min_length'] = $custom_error ?: "The {$this->current_field} field must be at least {$length} characters.";
        return $this;
    }

    public function max_length($length, $custom_error = null) {
        $this->rules[$this->current_field]['max_length'] = $length;
        $this->messages[$this->current_field]['max_length'] = $custom_error ?: "The {$this->current_field} field cannot exceed {$length} characters.";
        return $this;
    }

    public function valid_email($custom_error = null) {
        $this->rules[$this->current_field]['valid_email'] = true;
        $this->messages[$this->current_field]['valid_email'] = $custom_error ?: "The {$this->current_field} field must contain a valid email address.";
        return $this;
    }

    public function in_list($list, $custom_error = null) {
        $this->rules[$this->current_field]['in_list'] = explode(',', $list);
        $this->messages[$this->current_field]['in_list'] = $custom_error ?: "The {$this->current_field} field must be one of: {$list}.";
        return $this;
    }

    public function run() {
        foreach ($this->rules as $field => $field_rules) {
            $value = $this->data[$field] ?? null;
            $value = trim($value);
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

            foreach ($field_rules as $rule => $param) {
                $valid = true;

                if ($rule === 'required' && empty($value)) {
                    $valid = false;
                } elseif (empty($value)) {
                    continue;
                }

                switch ($rule) {
                    case 'matches':
                        $valid = $value === ($this->data[$param] ?? null);
                        break;
                    case 'is_unique':
                        list($table, $db_field) = $param;
                        $db = lava_instance()->db;
                        $sql = "SELECT COUNT(*) FROM $table WHERE $db_field = ?";
                        $valid = $db->count($sql, [$value]) == 0;
                        break;
                    case 'min_length':
                        $valid = strlen($value) >= $param;
                        break;
                    case 'max_length':
                        $valid = strlen($value) <= $param;
                        break;
                    case 'valid_email':
                        $valid = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                        break;
                    case 'in_list':
                        $valid = in_array($value, $param);
                        break;
                }

                if (!$valid) {
                    $this->add_error($field, $this->messages[$field][$rule]);
                }
            }
        }

        return empty($this->errors);
    }

    private function add_error($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function errors() {
        if (empty($this->errors)) {
            return '';
        }

        $html = '<div class="mb-4 bg-red-800 border border-red-600 text-red-200 px-4 py-3 rounded">';
        $html .= '<ul>';
        foreach ($this->errors as $field_errors) {
            foreach ($field_errors as $error) {
                $html .= '<li>' . htmlspecialchars($error) . '</li>';
            }
        }
        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }
}