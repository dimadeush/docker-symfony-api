<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/User/AttachUserGroupController.php
 */

namespace App\Controller\Api\User;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Resource\UserResource;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

/**
 * Class AttachUserGroupController
 *
 * @OA\Tag(name="User Management")
 *
 * @package App\Controller\Api\User
 */
class AttachUserGroupController
{
    private SerializerInterface $serializer;
    private UserResource $userResource;

    /**
     * Constructor
     */
    public function __construct(SerializerInterface $serializer, UserResource $userResource)
    {
        $this->serializer = $serializer;
        $this->userResource = $userResource;
    }

    /**
     * Attach specified user group to specified user, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/user/{user}/group/{userGroup}",
     *      requirements={
     *          "user" = "%app.uuid_v1_regex%",
     *          "userGroup" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"POST"},
     *  )
     *
     * @ParamConverter(
     *      "user",
     *      class="App\Resource\UserResource",
     *  )
     * @ParamConverter(
     *      "userGroup",
     *      class="App\Resource\UserGroupResource",
     *  )
     *
     * @Security("is_granted('ROLE_ROOT')")
     *
     * @OA\Parameter(
     *      name="user",
     *      in="path",
     *      required=true,
     *      description="User GUID",
     *      @OA\Schema(
     *          type="string",
     *          default="User GUID",
     *      )
     *  )
     * @OA\Parameter(
     *      name="userGroup",
     *      in="path",
     *      required=true,
     *      description="User Group GUID",
     *      @OA\Schema(
     *          type="string",
     *          default="User Group GUID",
     *      )
     *  )
     * @OA\Response(
     *      response=200,
     *      description="User groups (user already belongs to this group)",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              ref=@Model(
     *                  type=App\Entity\UserGroup::class,
     *                  groups={"UserGroup", "UserGroup.role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @OA\Response(
     *      response=201,
     *      description="User groups (user added to this group)",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              ref=@Model(
     *                  type=App\Entity\UserGroup::class,
     *                  groups={"UserGroup", "UserGroup.role"},
     *              ),
     *          ),
     *      ),
     *  )
     * @OA\Response(
     *     response=401,
     *     description="Invalid token (not found or expired)",
     *     @OA\JsonContent(
     *         type="object",
     *         example={"code": 401, "message": "JWT Token not found"},
     *         @OA\Property(property="code", type="integer", description="Error code"),
     *         @OA\Property(property="message", type="string", description="Error description"),
     *     ),
     *  )
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
    public function __invoke(User $user, UserGroup $userGroup): JsonResponse
    {
        $status = $user->getUserGroups()->contains($userGroup) ? Response::HTTP_OK : Response::HTTP_CREATED;
        $this->userResource->save($user->addUserGroup($userGroup));
        $groups = [
            'groups' => [
                'set.UserGroupBasic',
            ],
        ];

        return new JsonResponse(
            $this->serializer->serialize($user->getUserGroups()->getValues(), 'json', $groups),
            $status,
            [],
            true
        );
    }
}
