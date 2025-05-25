<?php
declare(strict_types=1);

namespace Technoquill\Framework\Asset;


use JsonException;
use Technoquill\Framework\View\View;
use Technoquill\Framework\Support\Helper\Html;

final class Asset
{

    /** @var array */
    protected array $assets = [];

    /** @var array */
    protected array $used = [];

    /** @var View */
    protected View $view;

    /** @var string  */
    private const REGEX_URL = "^(http|https)://[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,3}(/\S*)?^";


    /**
     * @param View $view
     * @throws JsonException
     */
    public function __construct(View $view)
    {
        $this->view = $view;
        $this->load();
    }

    /**
     * @return void
     * @throws JsonException
     */
    protected function load(): void
    {
        $asset = $this->view->getAssetPath() . '/' . 'asset.json';
        if (file_exists($asset)) {
            $list = json_decode(file_get_contents($asset), true, 512, JSON_THROW_ON_ERROR);
            $this->assets = $this->normalizeItems($list ?? []);
        }
    }

    /**
     * @return string
     */
    public function includeHeader(): string
    {
        return $this->renderLinkDefinitions();
    }

    public function includeFooter(): string
    {
        return $this->renderScriptDefinitions();
    }


    public function addInlineStyle(string $content = '', array $attributes = []): string
    {
        echo Html::style($content, $attributes);
        return '';
    }

    public function addInlineScript(string $content = '', array $attributes = []): string
    {
        echo Html::script(attributes: $attributes, content: $content, beautify: false);
        return '';
    }


    /**
     * @param string $url
     * @return string
     */
    public function source(string $url): string
    {
        if (str_starts_with($url, '/')) {
            $url = ltrim($url, '/');
        }
        return $this->relativePath() . '/' . $url;
    }


    private function renderLinkDefinitions(): string
    {
        $render = '';
        if (isset($this->assets['link'])) {
            usort($this->assets['link'], static function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });
            foreach ($this->assets['link'] as $asset) {
                extract($asset);
                $render .= Html::link(rel: $type, url: $url, attributes: $attributes);
            }
        }
        echo $render;
        return '';
    }


    private function renderScriptDefinitions(): string
    {
        $render = '';
        if (isset($this->assets['script'])) {
            usort($this->assets['script'], static function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });
            foreach ($this->assets['script'] as $asset) {
                extract($asset);
                $render .= Html::js(src: $url, attributes: $attributes);
            }
        }
        echo $render;
        return '';
    }

    /**
     * @param array $list
     * @return array
     */
    private function normalizeItems(array $list = []): array
    {
        $array = [];
        foreach ($list as $value) {
            $array[$value['definition']][] = [
                'name' => $value['name'],
                'type' => $value['type'],
                'url' => $this->normalizeUrl($value['url']),
                'position' => $value['position'],
                'dependencies' => $value['dependencies'] ?? [],
                'attributes' => $value['attributes'] ?? [],
            ];
        }
        return $array;
    }

    /**
     * @param string $url
     * @return string
     */
    private function normalizeUrl(string $url): string
    {
        if(!str_starts_with($url, '//') && str_starts_with($url, '/')) {
            $url = ltrim($url, '/');
        }
        if(preg_match(self::REGEX_URL, $url) || str_starts_with($url, '//')) {
            return  $url;
        }
        return $this->relativePath() . "/" . $url;
    }


    /**
     * @return string
     */
    private function relativePath(): string
    {
        return str_replace(base_path() . '/resources', '', $this->view->getAssetPath());
    }


}