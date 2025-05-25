<?php
declare(strict_types=1);

namespace Technoquill\Framework\Controller;

use Technoquill\Framework\Contract\ControllerInterface;
use Technoquill\Framework\Http\Response;
use Technoquill\Framework\View\View;
/**
 * Abstract class representing a web controller that handles rendering views
 * and managing parameters. This class is meant to be extended by specific
 * controllers to implement application logic.
 */
abstract class WebController implements ControllerInterface
{

    protected array $params = [];

    protected View $view;

    /**
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @param array $params
     * @return self
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Render HTML view
     *
     * @param string $route — route name (e.g., 'home/index')
     * @param array $params — variables to pass to the template
     * @param int $status — status code (default 200)
     * @return Response
     */
    protected function render(string $route, array $params = [], int $status = 200): Response
    {
        $content = $this->view->render($route, $params);
        return new Response($content, $status);
    }

    protected function renderPartial(string $route, array $params = [], int $status = 200): Response
    {
        $content = $this->view->renderPartial($route, $params);
        return new Response($content, $status);
    }

}