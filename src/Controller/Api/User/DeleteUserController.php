<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/User/DeleteUserController.php
 */

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Resource\UserResource;
use App\Rest\Controller;
use App\Rest\Traits\Methods;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Class DeleteUserController
 *
 * @OA\Tag(name="User Management")
 *
 * @package App\Controller\Api\User
 */
class DeleteUserController extends Controller
{
    use Methods\DeleteMethod;

    /**
     * Constructor
     */
    public function __construct(UserResource $resource)
    {
        $this->setResource($resource);
    }

    /**
     * Delete user entity, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/user/{requestUser}",
     *      requirements={
     *          "requestUser" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"DELETE"},
     *  )
     *
     * @ParamConverter(
     *     "requestUser",
     *     class="App\Resource\UserResource"
     *  )
     *
     * @Security("is_granted('ROLE_ROOT')")
     *
     * @OA\Response(
     *     response=200,
     *     description="deleted",
     *     @OA\JsonContent(
     *         ref=@Model(
     *             type=User::class,
     *             groups={"User"},
     *         ),
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
     *  )
     *
     * @throws Throwable
     */
    public function __invoke(Request $request, User $requestUser, User $loggedInUser): Response
    {
        if ($loggedInUser === $requestUser) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'You cannot remove yourself...');
        }

        return $this->deleteMethod($request, $requestUser->getId());
    }
}
