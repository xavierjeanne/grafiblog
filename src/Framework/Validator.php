<?php

namespace App\Framework;

use Framework\Database\Table;
use Framework\Validator\ValidationError;

class Validator
{
    /**
     *
     * @var array
     */
    private $params;

    /**
     *
     * @var string[]
     */
    private $errors = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * check if field required are in array
     *
     * @param string ...$keys
     * @return self
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }
    /**
     * check if key is not empty
     *
     * @param string ...$keys
     * @return self
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }
    /**
     * get error
     *
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * check if length field is valid
     *
     * @param string $key
     * @param integer|null $min
     * @param integer|null $max
     * @return self
     */
    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) && !is_null($max) && ($length < $min || $length > $max)) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) && $length < $min) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) && $length > $max) {
            $this->addError($key, 'maxLength', [$max]);
            return $this;
        }
        return $this;
    }
    /**
     * check if element is a slug
     *
     * @param string $key
     * @return self
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }
    public function dateTime(string $key, string $format = "Y-m-d H:i:s"): self
    {
        $value = $this->getValue($key);
        $date = \Datetime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }
    /**
     * add error
     *
     * @param string $key
     * @param string $rule
     * @return void
     */
    private function addError(string $key, string $rule, array $attributes = [])
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }

    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    public function exists(string $key, string $table, \PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT * FROM $table WHERE id=?");
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
        }
        return $this;
    }
}
