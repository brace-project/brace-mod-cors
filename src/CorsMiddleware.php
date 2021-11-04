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



        out ("Options", $request->getMethod());
        if ($request->getMethod() !== "OPTIONS") {
            $response = $handler->handle($request);
        } else {
            $response = $this->app->responseFactory->createResponseWithoutBody();
            out($response);
        }

        if ($origin !== "") {
            if ($this->allowOrigins !== null) {
                if (isset ($this->allowOrigins[$origin]) || in_array("*", $this->allowOrigins)) {
                    $response = $response->withAddedHeader("Access-Control-Allow-Origin", $origin);
                    foreach ($this->allowOrigins[$origin] ?? [] as $key => $value) {
                          $response = $response->withAddedHeader($key, $value);
                    }
                }
            }
        }
        $response = $response->withAddedHeader("Access-Control-Allow-Headers", "Content-Type, origin, accept, Cookie");
        $response = $response->withAddedHeader("Access-Control-Allow-Credentials", 'true');
        $response = $response->withAddedHeader("Access-Control-Max-Age", 0);
        return $response;
    }
}
