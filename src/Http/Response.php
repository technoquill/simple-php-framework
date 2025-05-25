<?php
declare(strict_types=1);

namespace Technoquill\Framework\Http;

use JsonException;

final class Response
{
    /** @var int  */
    protected int $status = 200;

    /** @var array  */
    protected array $headers = [];

    /** @var array  */
    protected array $cookies = [];

    /** @var string  */
    protected string $content = '';


    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     * @return $this
     */
    public function setCookie(string $name, string $value = '', array  $options = []): self
    {
        $this->cookies[] = [
            'name' => $name,
            'value' => $value,
            'options' => $options
        ];
        return $this;
    }

    /**
     * @param string $name
     * @param array $options
     * @return $this
     */
    public function deleteCookie(string $name, array $options = []): self
    {
        $options['expires'] = time() - 3600;
        return $this->setCookie($name, '', $options);
    }

    /**
     * @throws JsonException
     */
    public function json($data, int $status = 200): self
    {
        $this->setHeader('Content-Type', 'application/json');
        $this->setStatus($status);
        $this->setContent(json_encode($data, JSON_THROW_ON_ERROR));
        return $this;
    }

    /**
     * @return void
     */
    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        foreach ($this->cookies as $cookie) {
            $name    = $cookie['name'];
            $value   = $cookie['value'];
            $options = $cookie['options'];

            setcookie(
                $name,
                $value,
                $options['expires'] ?? 0,
                $options['path'] ?? '/',
                $options['domain'] ?? '',
                $options['secure'] ?? false,
                $options['httponly'] ?? false
            );
        }
        echo $this->content;
    }
}