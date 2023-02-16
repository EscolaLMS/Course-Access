<?php

namespace EscolaLms\CourseAccess\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\CourseAccess\Http\Controllers\Swagger\CoursesAccessAPISwagger;
use EscolaLms\CourseAccess\Http\Requests\AddAccessAPIRequest;
use EscolaLms\CourseAccess\Http\Requests\ListAccessAPIRequest;
use EscolaLms\CourseAccess\Http\Requests\RemoveAccessAPIRequest;
use EscolaLms\CourseAccess\Http\Requests\SetAccessAPIRequest;
use EscolaLms\CourseAccess\Http\Resources\UserGroupResource;
use EscolaLms\CourseAccess\Http\Resources\UserShortResource;
use EscolaLms\Courses\Models\Course;
use EscolaLms\CourseAccess\Services\Contracts\CourseAccessServiceContract;
use Illuminate\Http\JsonResponse;

class CourseAccessAPIController extends EscolaLmsBaseController implements CoursesAccessAPISwagger
{
    private CourseAccessServiceContract $courseAccessService;

    public function __construct(CourseAccessServiceContract $courseAccessService)
    {
        $this->courseAccessService = $courseAccessService;
    }

    public function list(ListAccessAPIRequest $request): JsonResponse
    {
        $course = $request->getCourse();

        return $this->sendAccessListResponse($course, __('Access List'));
    }

    public function add(AddAccessAPIRequest $request): JsonResponse
    {
        $course = $request->getCourse();
        $this->courseAccessService->addAccessForUsers($course, $request->input('users', []));
        $this->courseAccessService->addAccessForGroups($course, $request->input('groups', []));

        return $this->sendAccessListResponse($course->refresh(), __('Added to access list'));
    }

    public function remove(RemoveAccessAPIRequest $request): JsonResponse
    {
        $course = $request->getCourse();
        $this->courseAccessService->removeAccessForUsers($course, $request->input('users', []));
        $this->courseAccessService->removeAccessForGroups($course, $request->input('groups', []));

        return $this->sendAccessListResponse($course->refresh(), __('Removed from access list'));
    }

    public function set(SetAccessAPIRequest $request): JsonResponse
    {
        $course = $request->getCourse();
        if ($request->has('users')) {
            $this->courseAccessService->setAccessForUsers($course, $request->input('users'));
        }
        if ($request->has('groups')) {
            $this->courseAccessService->setAccessForGroups($course, $request->input('groups'));
        }

        return $this->sendAccessListResponse($course->refresh(), __('Set access list'));
    }

    private function sendAccessListResponse(Course $course, string $message): JsonResponse
    {
        return $this->sendResponse([
            'users' => UserShortResource::collection($course->users),
            'groups' => UserGroupResource::collection($course->groups),
        ], $message);
    }
}
