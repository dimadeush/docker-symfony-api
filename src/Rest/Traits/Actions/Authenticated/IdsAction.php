<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Authenticated/IdsAction.php
 */

namespace App\Rest\Traits\Actions\Authenticated;

use App\Rest\Traits\Methods\IdsMethod;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait IdsAction
 *
 * Trait to add 'idsAction' for REST controllers for authenticated users.
 *
 * @see \App\Rest\Traits\Methods\IdsMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Authenticated
 */
trait IdsAction
{
    // Traits
    use IdsMethod;

    /**
     * Find ids list, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *     path="/ids",
     *     methods={"GET"},
     *  )
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @OA\Response(
     *     response=200,
     *     description="success",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(type="string"),
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
    public function idsAction(Request $request): Response
    {
        return $this->idsMethod($request);
    }
}
