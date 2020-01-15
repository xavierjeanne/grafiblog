<?php

namespace Framework\Actions;

use PDO;
use Framework\Router;
use App\Blog\Entity\Post;
use App\Framework\Validator;
use Framework\Session\FlashService;
use Framework\Actions\RouterAwareAction;
use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction
{
    protected $viewPath;
    private $renderer;
    protected $table;
    private $router;
    private $flash;
    protected $routePrefix;
    protected $messages = [
        'create' => 'L\'élément a bien été crée',
        'edit' => 'L\élément a bien été modifié'

    ];
    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, Table $table, Router $router, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->router = $router;
        $this->flash = $flash;
    }
    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string) $request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return  $this->edit($request);
        }
        return $this->index($request);
    }
    /**
     * dispaly element list
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }
    /**
     * edit an element
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($item->id, $this->getParams($request, $item));
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $params = $request->getParsedBody();
            $params['id'] = $item->id;
            $item = $params;
        }
        return $this->renderer->render($this->viewPath . '/edit', $this->formParams(compact('item', 'errors')));
    }
    /**
     * create new element
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $item = $request->getParsedBody();
        }
        return $this->renderer->render($this->viewPath . '/create', $this->formParams(compact('item', 'errors')));
    }
    /**
     * delete an element
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $this->table->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix . '.index');
    }
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    protected function getParams(Request $request, $item): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }
    protected function getValidator(Request $request)
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }

    protected function getNewEntity()
    {
        return [];
    }
    /**
     * allow treat params send to view
     *
     * @param $params
     * @return array
     */
    protected function formParams($params): array
    {
        return $params;
    }
}
