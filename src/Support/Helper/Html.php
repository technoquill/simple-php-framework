<?php
declare(strict_types=1);

namespace Technoquill\Framework\Support\Helper;

class Html
{

    /**
     * @param string $rel
     * @param string $url
     * @param array $attributes
     * @return string
     */
    public static function link(string $rel, string $url, array $attributes = []): string
    {
        $linkAttributes = '';
        foreach ($attributes as $name => $attribute) {
            if ($name === $attribute || $attribute === true) {
                $linkAttributes .= " " . $name;
            } else {
                $linkAttributes .= ' ' . $name . '="' . $attribute . '"';
            }
        }
        return '<link rel="' . $rel . '" href="' . $url . '"' . $linkAttributes . '>' . "\n\t";
    }


    /**
     * @param string $url
     * @param string $type
     * @param array $attributes
     * @return string
     */
    public static function icon(string $url, string $type = "image/x-icon", array $attributes = []): string
    {
        return self::link('icon', $url, array_merge(['type' => $type], $attributes));
    }


    /**
     * @param string $type
     * @param string $src
     * @param array $attributes
     * @param string $content
     * @param bool $beautify
     * @return string
     */
    public static function script(string $type = '', string $src = '', array $attributes = [], string $content = '', bool $beautify = true): string
    {
        $scriptAttributes = $type !== '' ? ' type="' . $type . '"' : '';
        $scriptAttributes .= $src !== '' ? ' src="' . $src . '"' : '';
        if($beautify && $content !== '') {
            $content = "\n\t" . $content . "\n\t";
        }
        foreach ($attributes as $name => $attribute) {
            if ($name === $attribute || $attribute === true) {
                $scriptAttributes .= " " . $name;
            } else {
                $scriptAttributes .= ' ' . $name . '="' . $attribute . '"';
            }
        }
        return '<script' . $scriptAttributes . '>' . $content .'</script>' . "\n\t";
    }

    /**
     * @param string $content
     * @param array $attributes
     * @return string
     */
    public static function style(string $content = '', array $attributes = []): string
    {
        $styleAttributes = '';

        foreach ($attributes as $name => $attribute) {
            if ($name === $attribute || $attribute === true) {
                $styleAttributes .= " " . $name;
            } else {
                $styleAttributes .= ' ' . $name . '="' . $attribute . '"';
            }
        }

        return '<style' . $styleAttributes . '>' . $content  . "\t" . '</style>' . "\n\t";
    }


    /**
     * @param string $src
     * @param array $attributes
     * @param string $type
     * @return string
     */
    public static function js(string $src = '', array $attributes = [], string $type = ''): string
    {
        return self::script(type: $type, src: $src, attributes: $attributes);
    }

    /**
     * @param string $url
     * @param array $attributes
     * @return string
     */
    public static function stylesheet(string $url, array $attributes = []): string
    {
        return self::link('stylesheet', $url, $attributes);
    }


    /**
     * @param string $tag The HTML tag name.
     * @param string $content The content to be wrapped within the tag. Defaults to an empty string.
     * @param array $attributes An associative array of attributes to be added to the tag. Defaults to an empty array.
     * @return string The generated HTML tag with the specified content and attributes.
     */
    public static function tag(string $tag, string $content = '', array $attributes = []): string
    {
        return "<$tag>" . $content . "</$tag>";
    }


}