<?php

namespace Tests\Framework\Twig;

use PHPUnit\Framework\TestCase;
use Framework\Twig\FormExtension;

class FormExtensionTest extends TestCase
{
    private $formExtension;
    public function setUp(): void
    {
        $this->formExtension = new FormExtension();
    }
    public function testField()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre');
        $this->assertSimilar("<div class=\"form-group\"><label for=\"name\">Titre</label><input type=\"text\" class=\"form-control\" name=\"name\" id=\"name\" value=\"demo\"/></div>", $html);
    }
    public function testTextarea()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre', ['type' => 'textarea']);
        $this->assertSimilar("<div class=\"form-group\"><label for=\"name\">Titre</label><textarea class=\"form-control\" name=\"name\" id=\"name\" rows=\"10\">demo</textarea></div>", $html);
    }

    public function testFieldError()
    {
        $context = ['errors' => ['name' => 'erreur']];
        $html = $this->formExtension->field($context, 'name', 'demo', 'Titre');
        $this->assertSimilar("<div class=\"form-group has-danger\"><label for=\"name\">Titre</label><input type=\"text\" class=\"form-control is-invalid\" name=\"name\" id=\"name\" value=\"demo\"/><small class=\"form-text text-muted\">erreur</small></div>", $html);
    }
    public function testFieldWithClass()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre', ['class' => 'demo']);
        $this->assertSimilar("<div class=\"form-group\"><label for=\"name\">Titre</label><input type=\"text\" class=\"form-control demo\" name=\"name\" id=\"name\" value=\"demo\"/></div>", $html);
    }
    private function trim(string $string)
    {
        $lines = explode(PHP_EOL, $string);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }
    private function assertSimilar(string $expected, string $actual)
    {
        $this->assertEquals($this->trim($expected), $this->trim($actual));
    }
}
