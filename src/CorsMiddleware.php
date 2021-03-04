<?php


namespace Brace\CORS;


use Brace\Core\Base\BraceAbstractMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware extends BraceAbstractMiddleware
{

    public function __construct(
        private ?array $allowOrigins = null,
        private ?\Closure $validator = null
    ){}


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $origin = $request->getHeaderLine("Origin");

        $response = $handler->handle($request);
        if ($origin !== "") {
            if ($this->allowOrigins !== null) {
                if (isset ($this->allowOrigins[$origin])) {
                    $response = $response->withAddedHeader("Access-Control-Allow-Origin", $origin);
                    foreach ($this->allowOrigins[$origin] as $key => $value) {
                          $response = $response->withAddedHeader($key, $value);
                    }
                }
            }
        }
        $response = $response->withAddedHeader("Access-Control-Max-Age", 300);
        return $response;
    }
}