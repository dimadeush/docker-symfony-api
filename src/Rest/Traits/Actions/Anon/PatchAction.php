<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Anon/PatchAction.php
 */

namespace App\Rest\Traits\Actions\Anon;

use App\DTO\Interfaces\RestDtoInterface;
use App\Rest\Traits\Methods\PatchMethod;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait PatchAction
 *
 * Trait to add 'patchAction' for REST controllers for anonymous users.
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
     * Patch entity with new data, accessible for anonymous users.
     *
     * @Route(
     *      "/{id}",
     *      requirements={
     *          "id" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"PATCH"},
     *  )
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     * @SWG\Schema(
     *          type="object",
     *          example={},
     *      ),
     *  )
     *
     * @throws Throwable
     */
    public function patchAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->patchMethod($request, $restDto, $id);
    }
}
