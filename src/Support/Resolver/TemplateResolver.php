<?php
declare(strict_types=1);

namespace Technoquill\Framework\Support\Resolver;

use BadMethodCallException;
use Technoquill\Framework\Config\Config;
use Technoquill\Framework\Exceptions\TemplateNotFoundException;
use Technoquill\Framework\Http\Request;


/**
 * The TemplateResolver class is responsible for determining and resolving
 * the appropriate theme and its associated configuration settings based on
 * the incoming request URI and predefined resolution rules.
 *
 * It processes defined patterns to detect template, paths, layout, and
 * caching options for the application in dependency of the theme.
 *
 * @method string getTemplate()
 * @method string getLayout()
 * @method string getLayoutsPath()
 * @method string getViewsPath()
 * @method string getAssetsPath()
 * @method string getErrorsPath()
 * @method bool useLayout()
 * @method bool useCache()
 */
final class TemplateResolver
{
    /** @var string|null */
    protected ?string $template = null;

    /** @var string */
    protected string $viewsPath;

    /** @var string */
    protected string $assetsPath;

    /** @var string */
    protected string $errorsPath;

    /** @var string */
    protected string $layout;

    /** @var string */
    protected string $layoutsPath;

    /** @var bool */
    protected bool $useLayout;

    /** @var bool */
    protected bool $cache;


    /**
     * @param Config $config
     * @param Request $request
     */
    public function __construct(Config $config, Request $request)
    {
        $this->resolve($config, $request);
    }


    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $calledProperty = lcfirst(str_replace('get', '', $name));
        if (isset($this->$calledProperty)) {
            return $this->$calledProperty;
        }
        throw new BadMethodCallException("Method $name() not found!");
    }


    /**
     * Resolves and determines the appropriate theme based on the request URI
     * and the defined resolution rules.
     *
     * @param Config $config
     * @param Request $request
     * @return void
     */
    protected function resolve(Config $config, Request $request): void
    {
        $uri = $request->getUri();
        $resolver = $config->get('view.resolver');
        if (!str_ends_with($uri, "/")) {
            $uri .= "/";
        }
        uksort($resolver, static fn($a, $b) => strlen($b) - strlen($a));

        foreach ($resolver as $pattern => $theme) {
            $prefix = rtrim($pattern, '*');
            if (str_starts_with($uri, $prefix)) {
                $template = $resolver[$prefix . "*"];
                $this->template = $template;
                $this->assetsPath = $config->get("view.templates.$template.assets_path");
                $this->viewsPath = $config->get("view.templates.$template.views_path");
                $this->layoutsPath = $config->get("view.templates.$template.layouts_path");
                $this->layout = $config->get("view.templates.$template.layout");
                $this->errorsPath = $config->get("view.templates.$template.errors_path");
                $this->useLayout = $config->get("view.templates.$template.use_layout");
                $this->cache = $config->get("view.templates.$template.cache");
                return;
            }
        }
        throw new TemplateNotFoundException("No theme matched for uri [$uri]");
    }

}