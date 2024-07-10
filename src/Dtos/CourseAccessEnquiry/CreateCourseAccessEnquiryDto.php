<?php

namespace EscolaLms\CourseAccess\Dtos\CourseAccessEnquiry;

use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\CourseAccess\Enum\EnquiryStatusEnum;
use Illuminate\Http\Request;

class CreateCourseAccessEnquiryDto extends CourseAccessEnquiryDto implements InstantiateFromRequest
{
    public static function instantiateFromRequest(Request $request): self
    {
        return new self(
            $request->input('course_id'),
            auth()->id(),
            $request->input('data', []),
            EnquiryStatusEnum::PENDING,
        );
    }
}
