<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Admin/PatchAction.php
 */

namespace App\Rest\Traits\Actions\Admin;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Traits\Methods\PatchMethod;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait PatchAction
 *
 * Trait to add 'patchAction' for REST controllers for 'ROLE_ADMIN' users.
 *
 * @see \App\Rest\Traits\Methods\PatchMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Root
 */
trait PatchAction
{
    // Traits
    use PatchMethod;

    /**
     * Patch entity with new data, accessible only for 'ROLE_ADMIN' users.
     *
     * @Route(
     *      "/{id}",
     *      requirements={
     *          "id" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"PATCH"},
     *  )
     *
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @OA\RequestBody(
     *      request="body",
     *      description="object",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"param": "value"},
     *      )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         type="object",
     *         example={},
     *     ),
     * )
     * @OA\Response(
     *     response=403,
     *     description="Access denied",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"code": 403, "message": "Access denied"},
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     * )
     *
     * @throws Throwable
     */
    public function patchAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->patchMethod($request, $restDto, $id);
    }
}
