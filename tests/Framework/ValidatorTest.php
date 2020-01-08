<?php

namespace Tests\Framework;


use App\Framework\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{

    private function makeValidator(array $params)
    {
        return new Validator($params);
    }

    public function testRequiredIfFail()
    {
        $errors  = $this->makeValidator([
            'name' => 'joe'
        ])
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testNotEmpty()
    {
        $errors  = $this->makeValidator([
            'name' => 'joe',
            'content' => ''
        ])
            ->notEmpty('content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testRequiredIfSuccess()
    {
        $errors  = $this->makeValidator([
            'name' => 'joe',
            'content' => 'content'
        ])
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testSlugSuccess()
    {
        $errors  = $this->makeValidator([
            'slug' => 'aze-eae-aeeae34'
        ])
            ->slug('slug')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testSlugErrors()
    {
        $errors  = $this->makeValidator([
            'slug1' => 'jaze-aeae-aReae34',
            'slug2' => 'jaz34e-aeae_aeeae',
            'slug3' => 'jaze--aeae-aeeae'
        ])
            ->slug('slug1')
            ->slug('slug2')
            ->slug('slug3')
            ->slug('slug4')
            ->getErrors();
        $this->assertCount(3, $errors);
    }

    public function testLength()
    {
        $params = [
            'slug' => '123456789'
        ];
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
        $errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champ slug doit contenir plus de 12 caractères', (string) $errors['slug']);
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 12)->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 4)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3, 20)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', null, 20)->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', null, 8)->getErrors());
    }

    public function testDatetime()
    {
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 11:12:25'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2012-12-12'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2012-18-12'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-02-29 11::12:13'])->dateTime('date')->getErrors());
    }
}
