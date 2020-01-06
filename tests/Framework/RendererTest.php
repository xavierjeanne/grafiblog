<?php

namespace Tests\Framework;

use Framework\Renderer;
use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase
{
    private $renderer;

    public function setUp(): void
    {
        $this->renderer = new Renderer();
        $this->renderer->addPath(__DIR__ . '/Views');
    }

    public function testRenderTheRiqhtPath()
    {
        $this->renderer->addPath('blog', __DIR__ . '/Views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Salut les gens', $content);
    }
    public function testRenderTheDefaultPath()
    {
        $content = $this->renderer->render('demo');
        $this->assertEquals('Salut les gens', $content);
    }
    public function testRenderWithParams()
    {
        $content = $this->renderer->render('demoparams', ['nom' => 'Xavier']);
        $this->assertEquals('Salut Xavier', $content);
    }
    public function testGlobalParameters()
    {
        $this->renderer->addGlobal('nom', 'Xavier');
        $content = $this->renderer->render('demoparams');
        $this->assertEquals('Salut Xavier', $content);
    }
}
