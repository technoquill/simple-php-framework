<?php
declare(strict_types=1);

namespace Technoquill\Framework\Http;

final class Request
{
    /** @var string  */
    protected string $method;

    /** @var string  */
    protected string $uri;

    /** @var array  */
    protected array $query;

    /** @var array  */
    protected array $post;

    /** @var array  */
    protected array $cookies;

    /** @var array  */
    protected array $files;

    /** @var array  */
    protected array $server;

    /** @var array  */
    protected array $headers;

    public function __construct(
        string $method,
        string $uri,
        array  $query,
        array  $post,
        array  $cookies,
        array  $files,
        array  $server,
        array  $headers
    )
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->query = $query;
        $this->post = $post;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
        $this->headers = $headers;
    }

    // Factory for globals
    public static function createFromGlobals(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            strtok($_SERVER['REQUEST_URI'] ?? '/', '?'),
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER,
            function_exists('getallheaders') ? getallheaders() : self::parseHeadersFromServer($_SERVER)
        );
    }

    protected static function parseHeadersFromServer(array $server): array
    {
        $headers = [];
        foreach ($server as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }


    /**
     * Retrieves the method string.
     *
     * @return string The method associated with this instance.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Retrieves the URI.
     *
     * @return string The URI as a string.
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Retrieves the query parameters.
     *
     * @return array The query parameters as an associative array.
     */
    public function getQueryParams(): array
    {
        return $this->query;
    }

    /**
     * Retrieves the parsed body of the request.
     *
     * @return array The parsed body data of the request as an associative array.
     */
    public function getParsedBody(): array
    {
        return $this->post;
    }

    /**
     * Retrieves the cookie parameters of the request.
     *
     * @return array The cookie parameters as an associative array.
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * Retrieves the files associated with the request.
     *
     * @return array The files data as an associative array.
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Retrieves the server parameters.
     *
     * @return array An associative array containing server parameters.
     */
    public function getServerParams(): array
    {
        return $this->server;
    }

    /**
     * Retrieves the headers.
     *
     * @return array An associative array containing the headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Retrieves a value from the query- or post-data based on the given key.
     *
     * @param string $key The key to retrieve the value for.
     * @param mixed|null $default The default value to return if the key is not found.
     * @return mixed The value associated with the key, or the default value if not found.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $this->post[$key] ?? $default;
    }
}