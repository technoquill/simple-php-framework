<?php
declare(strict_types=1);

namespace Technoquill\Framework\Asset;


use JsonException;
use Technoquill\Framework\Exceptions\FileNotFoundException;
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

    /** @var string */
    private const REGEX_URL = "^(http|https)://[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,3}(/\S*)?^";


    /**
     * @param View $view
     * @throws JsonException
     */
    public function __construct(View $view)
    {
        $this->view = $view;
        $this->setAssets();
    }


    /**
     * @throws JsonException
     */
    private function setAssets(): void
    {
        $asset = $this->view->getAssetPath() . '/' . 'asset.json';
        if (file_exists($asset)) {
            $list = json_decode(file_get_contents($asset), true, 512, JSON_THROW_ON_ERROR);
            foreach ($list as $key => $item) {
                if (!$this->isExternalUrl($item['url']) && !file_exists($this->publicAssetsPath() . "/" . $item['url'])) {
                    unset($list[$key]);
                }
            }
        } else {
            throw new FileNotFoundException('asset.json');
        }
        $this->assets = $this->normalizeItems($list);
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
        return $this->publicRelativeAssetsPath() . '/' . $url;
    }


    /**
     * @return string
     */
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


    /**
     * @return string
     */
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
        if (!str_starts_with($url, '//') && str_starts_with($url, '/')) {
            $url = ltrim($url, '/');
        }
        //if(preg_match(self::REGEX_URL, $url) || str_starts_with($url, '//')) {
        if ($this->isExternalUrl($url)) {
            return $url;
        }
        return $this->publicRelativeAssetsPath() . "/" . $url;
    }

    private function isExternalUrl(string $url): bool
    {
        return preg_match(self::REGEX_URL, $url) || str_starts_with($url, '//');
    }

    private function publicRelativeAssetsPath(): string
    {
        return '/assets/' . $this->view->getTemplate();
    }


    private function publicAssetsPath(): string
    {
        return base_path() . '/public/assets/' . $this->view->getTemplate();
    }


}