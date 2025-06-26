<?php

namespace App\Exports;

use App\Models\CoursesFile;
use App\Models\FolderName;
use App\Models\UserLogin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllReportNotPassed implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $facultyInfo;

    public function __construct()
    {
        $this->facultyInfo = $this->getFacultyInfo();
    }

    public function collection()
    {
        $folderIds = FolderName::pluck('folder_name_id');
        \Log::info('Folder IDs:', $folderIds->toArray());

        $submittedFolderIds = CoursesFile::whereNotNull('user_login_id')
            ->pluck('folder_name_id')
            ->unique();
        \Log::info('Submitted Folder IDs:', $submittedFolderIds->toArray());

        $notPassedFolderIds = $folderIds->diff($submittedFolderIds);
        \Log::info('Not Passed Folder IDs:', $notPassedFolderIds->toArray());

        $facultyMembers = UserLogin::where('role', 'faculty')->get();
        \Log::info('Faculty Members:', $facultyMembers->toArray());

        $notPassedFaculty = collect();

        foreach ($facultyMembers as $faculty) {
            $notPassedFolders = FolderName::whereIn('folder_name_id', $notPassedFolderIds)->get();
            foreach ($notPassedFolders as $folder) {
                $notPassedFaculty->push([
                    'full_name' => $this->facultyInfo['faculty']['first_name'] . ' ' . 
                                   $this->facultyInfo['faculty']['middle_name'] . ' ' . 
                                   $this->facultyInfo['faculty']['last_name'],
                                   'main_folder_name' => $folder->main_folder_name,  
                    'not_passed_folder' => $folder->folder_name,
            
                    'semester' => $this->facultyInfo['faculty']['subjects'][0]['semester']['semester'] 
                ]);
            }
        }

        \Log::info('Not Passed Faculty:', $notPassedFaculty->toArray());

        return $notPassedFaculty;
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Main Requirements',
            'Folder Name',
            'Semester',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                $sheet->getStyle('A1:D1')->getFont()->setBold(true);
                $sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
                $sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                
                $maxWidth = 50;
                foreach ($sheet->getColumnDimensions() as $column) {
                    if ($column->getWidth() > $maxWidth) {
                        $column->setWidth($maxWidth);
                        $column->setAutoSize(false);
                    }
                }
            },
        ];
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
