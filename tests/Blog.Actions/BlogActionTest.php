<?php

namespace Tests\Blog\Actions;

use PDO;
use stdClass;
use Framework\Router;
use Prophecy\Argument;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use PHPUnit\Framework\TestCase;
use App\Blog\Actions\BlogAction;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Renderer\RendererInterface;

class BlogActionTest extends TestCase
{
    private $action;
    private $postTable;
    private $renderer;
    private $router;

    public function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->postTable = $this->prophesize(PostTable::class);
        $this->router = $this->prophesize(Router::class);
        $this->action = new BlogAction(
            $this->renderer->reveal(),
            $this->postTable->reveal(),
            $this->router->reveal()
        );
    }
    public function makePost(int $id, string $slug): Post
    {

        $post = new Post();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }
    public function testShowRedirect()
    {

        $post = $this->makePost(9, "jdejdfej");
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');
        $this->router->generateUri('blog.show', ['id' => $post->id, 'slug' => $post->slug])->willReturn('/demo2');
        $this->postTable->find($post->id)->willReturn($post);
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('location'));
    }
    public function testShowRender()
    {

        $post = $this->makePost(9, "jdejdfej");
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');
        $this->postTable->find($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }
}
