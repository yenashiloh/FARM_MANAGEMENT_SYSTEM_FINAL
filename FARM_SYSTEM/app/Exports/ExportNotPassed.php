<?php

namespace App\Exports;

use App\Models\CoursesFile;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\UserLogin;
use App\Models\FolderName;

class ExportNotPassed implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $folderNameId;

    public function __construct($folderNameId)
    {
        $this->folderNameId = $folderNameId;
    }

    public function collection()
    {
        $submittedUserIds = CoursesFile::where('folder_name_id', $this->folderNameId)
            ->whereNotNull('user_login_id')
            ->pluck('user_login_id');
    
        return UserLogin::whereNotIn('user_login_id', $submittedUserIds)->get();
    }
    
    public function headings(): array
    {
        return [
            'Faculty Name',
            'Main Requirements',
        ];
    }

    public function map($userLogin): array
    {
        $facultyInfo = $this->getFacultyInfo();
        $faculty = $facultyInfo['faculty'];
    
        $coursesFile = CoursesFile::where('folder_name_id', $this->folderNameId)
            ->where('user_login_id', $userLogin->user_login_id)
            ->first();

        return [
            $faculty['first_name'] . ' ' . 
            $faculty['middle_name'] . ' ' . 
            $faculty['last_name'],
            FolderName::find($this->folderNameId)->folder_name ?? 'Unknown Folder',
           
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        return [];
    }

    private function getFacultyInfo()
    {
        $semester = [
            "id" => 1,
            "semester" => "1st Semester 2024-2025",
            "user_login_id" => 2
        ];
    
        $programs = [
            "Bachelor of Science in Applied Mathematics (BSAM)",
            "Bachelor of Science in Information Technology (BSIT)",
            "Bachelor of Science in Entrepreneurship (BSENTREP)"
        ];
    
        $subjects = [
            [
                "id" => 1,
                "code" => "MGT101",
                "name" => "Principles of Management and Organization",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "1st Year",
                        "program" => "BSAM"
                    ]
                ]
            ],
            [
                "id" => 2,
                "code" => "IT202",
                "name" => "Applications Development and Emerging Technologies",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "2nd Year",
                        "program" => "BSIT"
                    ]
                ]
            ],
            [
                "id" => 3,
                "code" => "ENT301",
                "name" => "Technopreneurship",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "3rd Year",
                        "program" => "BSENTREP"
                    ]
                ]
            ],
            [
                "id" => 4,
                "code" => "SYS202",
                "name" => "Systems Analysis and Design",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "2nd Year",
                        "program" => "BSIT"
                    ]
                ]
            ],
            [
                "id" => 5,
                "code" => "CS303",
                "name" => "Computer Science",
                "semester" => $semester,
                "year_programs" => [
                    [
                        "year" => "4th Year",
                        "program" => "BSIT"
                    ],
                    [
                        "year" => "3rd Year",
                        "program" => "BSAM"
                    ]
                ]
            ]
        ];
    
        $data = [
            "faculty" => [
                "faculty_id" => 2,
                "first_name" => "Diana",
                "middle_name" => "M.",
                "last_name" => "Rose",
                "programs" => $programs,
                "subjects" => $subjects
            ]
        ];
    
        return $data;
    }
}
