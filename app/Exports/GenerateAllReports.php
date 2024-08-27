<?php

namespace App\Exports;

use App\Models\CoursesFile;
use App\Models\FolderName;
use App\Models\UserLogin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class GenerateAllReports implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $facultyInfo;
    protected $mainFolders;
    protected $subFolders;
    protected $semester;

    public function __construct($semester)
    {
        $this->semester = $semester;
        $this->facultyInfo = $this->getFacultyInfo();
        
        $this->mainFolders = ['Test Administration', 'Classroom Management', 'Syllabus Preparation'];
        
        $allFolders = FolderName::orderBy('main_folder_name')
            ->orderBy('folder_name')
            ->get();
        
        $this->subFolders = [];
        foreach ($this->mainFolders as $mainFolder) {
            $this->subFolders[$mainFolder] = $allFolders
                ->where('main_folder_name', $mainFolder)
                ->values()
                ->toArray();
        }
    }
    
    public function collection()
    {
        $data = collect();
        
        $rowData = [
            'no' => 1,
            'date_submitted' => $this->getLatestSubmissionDate(),
            'faculty_name' => $this->facultyInfo['faculty']['first_name'] . ' ' . 
                              $this->facultyInfo['faculty']['middle_name'] . ' ' . 
                              $this->facultyInfo['faculty']['last_name'],
            'semester' => $this->semester,
        ];
        
        foreach ($this->mainFolders as $mainFolder) {
            foreach ($this->subFolders[$mainFolder] as $subFolder) {
                $fileCount = CoursesFile::where('folder_name_id', $subFolder['folder_name_id'])
                    ->where('user_login_id', $this->facultyInfo['faculty']['faculty_id'])
                    ->where('semester', $this->semester)
                    ->count();
                
                $key = $mainFolder . '|' . $subFolder['folder_name'];
                $rowData[$key] = $fileCount > 0 ? $fileCount : 'X';
            }
        }
        
        $data->push($rowData);
        
        return $data;
    }

    public function headings(): array
    {
        $headers = ['No.', 'Date Submitted', 'Faculty Name'];

        foreach ($this->mainFolders as $mainFolder) {
            foreach ($this->subFolders[$mainFolder] as $subFolder) {
                $headers[] = $subFolder['folder_name'];
            }
        }

        $headers[] = 'Semester';

        return $headers;
    }

    public function map($row): array
    {
        $mappedRow = [
            $row['no'],
            $row['date_submitted'],
            $row['faculty_name']
        ];

        foreach ($this->mainFolders as $mainFolder) {
            foreach ($this->subFolders[$mainFolder] as $subFolder) {
                $key = $mainFolder . '|' . $subFolder['folder_name'];
                $mappedRow[] = $row[$key];
            }
        }

        $mappedRow[] = $row['semester'];

        return $mappedRow;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        
        $startCol = 'D';
        $endCol = $startCol;
        
        $folderColors = [
            'Test Administration' => 'C6EFCE',
            'Classroom Management' => 'F9C3C3',
            'Syllabus Preparation' => 'FFEFB6',
        ];
        
        foreach ($this->mainFolders as $mainFolder) {
            $subFolderCount = count($this->subFolders[$mainFolder]);
            $endCol = chr(ord($startCol) + $subFolderCount - 1);
            
            $sheet->setCellValue("{$startCol}1", strtoupper($mainFolder));
            $sheet->mergeCells("{$startCol}1:{$endCol}1");
            $sheet->getStyle("{$startCol}1:{$endCol}1")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $folderColors[$mainFolder]]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            
            $sheet->getStyle("{$startCol}2:{$endCol}2")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $folderColors[$mainFolder]]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            
            $startCol = chr(ord($endCol) + 1);
        }
        
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE],
        ]);
        
        $sheet->setCellValue('A1', '');
        $sheet->setCellValue('B1', '');
        $sheet->setCellValue('C1', '');
        $sheet->setCellValue($lastColumn . '1', '');
        
        $sheet->fromArray($this->headings(), null, 'A2');
       
        $sheet->getStyle("A2:C2")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        
        $sheet->getStyle("{$lastColumn}2")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        
        $sheet->fromArray($this->map($this->collection()->first()), null, 'A3');
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A3:{$highestColumn}{$highestRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        
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

    private function getLatestSubmissionDate()
    {
        $latestFile = CoursesFile::where('user_login_id', $this->facultyInfo['faculty']['faculty_id'])
            ->where('semester', $this->semester)
            ->latest('created_at')
            ->first();

        return $latestFile ? $latestFile->created_at->setTimezone('Asia/Manila')->format('F d, Y, h:i A') : 'N/A';
    }
}