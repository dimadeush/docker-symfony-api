<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Authenticated/FindAction.php
 */

namespace App\Rest\Traits\Actions\Authenticated;

use App\Rest\Traits\Methods\FindMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Trait FindAction
 *
 * Trait to add 'findAction' for REST controllers for authenticated users.
 *
 * @see \App\Rest\Traits\Methods\FindMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Authenticated
 */
trait FindAction
{
    // Traits
    use FindMethod;

    /**
     * Get list of entities, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @Route(
     *     path="",
     *     methods={"GET"},
     *  )
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     * @SWG\Schema(
     *          type="array",
     * @SWG\Items(type="string"),
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     * @SWG\Schema(
     *          type="object",
     * @SWG\Property(property="code", type="integer", description="Error code"),
     * @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @throws Throwable
     */
    public function findAction(Request $request): Response
    {
        return $this->findMethod($request);
    }
}
