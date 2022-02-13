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

        if ($request->getMethod() !== "OPTIONS") {
            $response = $handler->handle($request);
        } else {
            $response = $this->app->responseFactory->createResponseWithoutBody();
        }

        $headersToSet = [
            "Access-Control-Allow-Headers" => "Content-Type, origin, accept, Cookie",
            "Access-Control-Allow-Credentials" => 'true',
            "Access-Control-Max-Age" => 0
        ];
        
        if ($origin !== "") {
            if ($this->allowOrigins !== null) {
                if (isset ($this->allowOrigins[$origin]) || in_array("*", $this->allowOrigins)) {
                    if ( ! $response->hasHeader("Access-Control-Allow-Origin"))
                        $response = $response->withAddedHeader("Access-Control-Allow-Origin", $origin);
                    foreach ($this->allowOrigins[$origin] ?? [] as $key => $value) {
                        $response = $response->withAddedHeader($key, $value);
                    }
                }
            }
            if ($this->validator !== null) {
                if (phore_di_call($this->validator, $this->app, ["origin" => $origin]) == true) {
                    if ( ! $response->hasHeader("Access-Control-Allow-Origin"))
                        $response = $response->withAddedHeader("Access-Control-Allow-Origin", $origin);
                    foreach ($this->allowOrigins[$origin] ?? [] as $key => $value) {
                        $response = $response->withAddedHeader($key, $value);
                    }
                }
            }
        }

        foreach ($headersToSet as $key => $value)
            if ( ! $response->hasHeader($key))
                $response = $response->withAddedHeader($key, $value);

        return $response;
    }
}
