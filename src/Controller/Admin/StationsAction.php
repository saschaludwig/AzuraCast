<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\SingleActionInterface;
use App\Http\Response;
use App\Http\ServerRequest;
use App\Radio\Adapters;
use App\VueComponent\StationFormComponent;
use Psr\Http\Message\ResponseInterface;

final class StationsAction implements SingleActionInterface
{
    public function __construct(
        private readonly StationFormComponent $stationFormComponent,
        private readonly Adapters $adapters
    ) {
    }

    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $router = $request->getRouter();

        return $request->getView()->renderVuePage(
            response: $response,
            component: 'Admin/Stations',
            id: 'admin-stations',
            title: __('Stations'),
            props: array_merge(
                $this->stationFormComponent->getProps($request),
                [
                    'listUrl' => $router->fromHere('api:admin:stations'),
                    'frontendTypes' => $this->adapters->listFrontendAdapters(),
                    'backendTypes' => $this->adapters->listBackendAdapters(),
                ]
            )
        );
    }
}
