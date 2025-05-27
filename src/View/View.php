<?php
declare(strict_types=1);

namespace Technoquill\Framework\View;

use RuntimeException;
use Technoquill\Framework\Exceptions\FileNotFoundException;
use Technoquill\Framework\Support\Resolver\TemplateResolver;

final class View
{

    protected TemplateResolver $resolver;

    /**
     * @param TemplateResolver $resolver
     */
    public function __construct(TemplateResolver $resolver)
    {
        $this->resolver = $resolver;
    }


    /**
     * @return string
     */
    public function getAssetPath(): string
    {
        return $this->resolver->getAssetsPath();
    }


    public function getTemplate(): string
    {
        return $this->resolver->getTemplate();
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function render(string $route, array $params = []): string
    {
        $templatePath = rtrim($this->resolver->getViewsPath(), '/') . '/' . ltrim($route, '/') . '.php';

        // Checks is the template available
        if (!file_exists($templatePath)) {
            throw new FileNotFoundException($templatePath);
        }

        $content = $this->renderFile($templatePath, $params);

        // If layout does not need, return content
        if (empty($this->resolver->useLayout())) {
            return $content;
        }

        // Create absolut way to layout
        $layoutPath = rtrim($this->resolver->getLayoutsPath(), '/') . '/' . $this->resolver->getLayout() . '.php';

        // Checks is the layout available
        if (!file_exists($layoutPath)) {
            throw new FileNotFoundException($layoutPath);
        }

        // Render layout and content
        return $this->renderFile($layoutPath, array_merge($params, ['content' => $content]));
    }

    /**
     * Render a PHP template file
     *
     * @param string $route route name (e.g. 'home/partial')
     * @param array $params Data to pass to the template
     * @return string Ready-made HTML content
     */
    public function renderPartial(string $route, array $params = []): string
    {
        $route = trim($route, "/ \\");
        if ($route === '') {
            throw new RuntimeException("Partial template name is required.");
        }
        if (str_contains($route, '..')) {
            throw new RuntimeException("Invalid partial template name.");
        }
        $file = $this->resolver->getViewsPath() . '/' . $route . '.php';
        return $this->renderFile($file, $params);
    }


    /**
     * Renders a PHP file by loading the specified template and injecting parameters.
     *
     * @param string $file The template name, which can include a namespace (e.g. 'namespace::template') or a file path without extension.
     * @param array $params An associative array of parameters to be extracted into the template file.
     * @return string The rendered output as a string.
     * @throws RuntimeException If the namespace is undefined or the template file is not found.
     */
    protected function renderFile(string $file, array $params): string
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }
        extract($params, EXTR_SKIP);

        ob_start();
        require $file;
        return ob_get_clean();
    }


}