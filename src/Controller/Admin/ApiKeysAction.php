<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\SingleActionInterface;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class ApiKeysAction implements SingleActionInterface
{
    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $router = $request->getRouter();

        return $request->getView()->renderVuePage(
            response: $response,
            component: 'Admin/ApiKeys',
            id: 'admin-api-keys',
            title: __('API Keys'),
            props: [
                'apiUrl' => $router->named('api:admin:api-keys'),
                'myApiKeysUrl' => $router->named('profile:index'),
            ],
        );
    }
}
