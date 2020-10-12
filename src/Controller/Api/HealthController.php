<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/HealthController.php
 */

namespace App\Controller\Api;

use App\Rest\ResponseHandler;
use App\Service\HealthService;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class HealthController
 *
 * @SWG\Tag(name="Tools")
 *
 * @package App\Controller\Api
 */
class HealthController
{
    private ResponseHandler $responseHandler;
    private HealthService $healthService;

    /**
     * Constructor
     */
    public function __construct(ResponseHandler $responseHandler, HealthService $healthService)
    {
        $this->responseHandler = $responseHandler;
        $this->healthService = $healthService;
    }

    /**
     * Some simple tasks to ensure that application is up and running like expected.
     *
     * @see https://kubernetes.io/docs/tasks/configure-pod-container/configure-liveness-readiness-probes/
     *
     * @Route(
     *     path="/health",
     *     methods={"GET"}
     *  )
     *
     * @SWG\Get(security={})
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     * @SWG\Schema(
     *          type="object",
     *          example={"timestamp": "2019-08-01T09:00:00+00:00"},
     * @SWG\Property(property="timestamp", type="string"),
     *      ),
     *  )
     *
     * @throws Throwable
     */
    public function __invoke(Request $request): Response
    {
        return $this->responseHandler->createResponse(
            $request,
            $this->healthService->check(),
            null,
            Response::HTTP_OK,
            ResponseHandler::FORMAT_JSON,
            [
                'groups' => [
                    'Health.timestamp',
                ],
            ]
        );
    }
}
