<?php

namespace Framework\Twig;

use ReflectionFunctionAbstract;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }
    /**
     * generate field code html
     *
     * @param array $context context of twig view
     * @param string $key field key
     * @param mixed $value field value
     * @param string $label label of field
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, $value, ?string $label = null, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $class = 'form-group';
        $value = $this->convertValue($value);
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name' => $key,
            'id' => $key
        ];
        if ($error) {
            $class .= ' has-danger';
            $attributes['class']  .= ' is-invalid';
        }
        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        return "<div class=\"" .  $class . "\"><label for=\"name\">{$label}</label>{$input}{$error}</div>";
    }
    /**
     * generate html with context error
     *
     * @param  $context
     * @param  $key
     * @return string
     */
    private function getErrorHtml($context, $key)
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return "<small class=\"form-text text-muted\">{$error}</small>";
        }
        return "";
    }

    /**
     * generate input field
     *
     * @param string|null $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input type=\"text\" " . $this->getHmtlFromArray($attributes) . " value=\"{$value}\"/>";
    }

    /**
     * generate textarea field
     *
     * @param string|null $value
     * @param array $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHmtlFromArray($attributes) . " rows=\"10\">{$value}</textarea>";
    }

    private function select(?string $value, array $options, array $attributes)
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHmtlFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");
        return "<select " . $this->getHmtlFromArray($attributes) . ">$htmlOptions</select>";
    }
    private function getHmtlFromArray(array $attributes)
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string) $key;
            } elseif ($value !== false) {
                $htmlParts[] =  "$key=\"$value\"";
                ;
            }
        }
        return implode(' ', $htmlParts);
    }
    private function convertValue($value): string
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string) $value;
    }
}
