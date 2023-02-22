<?php

namespace EscolaLms\CourseAccess\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\CourseAccess\Database\Seeders\CourseAccessPermissionSeeder;
use EscolaLms\CourseAccess\Enum\CourseAccessPermissionEnum;
use EscolaLms\CourseAccess\Events\CourseAccessEnquiryAdminCreatedEvent;
use EscolaLms\CourseAccess\Events\CourseAccessEnquiryStudentCreatedEvent;
use EscolaLms\CourseAccess\Models\Course;
use EscolaLms\CourseAccess\Models\CourseAccessEnquiry;
use EscolaLms\CourseAccess\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CourseAccessEnquiryCreateApiTest extends TestCase
{
    use CreatesUsers;

    private $course;
    private $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CourseAccessPermissionSeeder::class);
        $this->course = Course::factory()->create();
        $this->student = $this->makeStudent();
    }

    public function testCourseAccessEnquiryCreateUnauthorized(): void
    {
        $this->postJson('api/course-access-enquiries', [
            'course_id' => $this->course->getKey(),
        ])->assertUnauthorized();
    }

    public function testCourseAccessEnquiryCreate(): void
    {
        Event::fake([CourseAccessEnquiryStudentCreatedEvent::class]);

        $data = json_encode(['field1' => 'value_1']);

        $this->actingAs($this->student, 'api')
            ->postJson('api/course-access-enquiries', [
                'course_id' => $this->course->getKey(),
                'data' => $data,
            ])->assertCreated();

        $this->assertDatabaseHas('course_access_enquiries', [
            'course_id' => $this->course->getKey(),
            'user_id' => $this->student->getKey(),
        ]);

        Event::fake([CourseAccessEnquiryStudentCreatedEvent::class]);
    }

    public function testShouldThrowErrorWhenCourseAccessEnquiryAlreadyExists(): void
    {
        CourseAccessEnquiry::factory()
            ->state([
                'course_id' => $this->course->getKey(),
                'user_id' => $this->student->getKey(),
            ])
            ->create();

        $this->actingAs($this->student, 'api')->postJson('api/course-access-enquiries', [
            'course_id' => $this->course->getKey(),
        ])->assertStatus(400);
    }

    public function testShouldDispatchEventToAdminAfterCreatingCourseAccessEnquiry(): void
    {
        Event::fake([CourseAccessEnquiryAdminCreatedEvent::class]);

        $this->makeAdmin();

        $this->actingAs($this->student, 'api')->postJson('api/course-access-enquiries', [
            'course_id' => $this->course->getKey(),
        ])->assertCreated();

        Event::assertDispatched(function (CourseAccessEnquiryAdminCreatedEvent $event) {
            return $event->user->hasPermissionTo(CourseAccessPermissionEnum::APPROVE_COURSE_ACCESS_ENQUIRY);
        });
    }
}