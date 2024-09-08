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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateAllReports implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
   
    protected $mainFolders;
    protected $subFolders;
    protected $semester;

    public function __construct($semester)
    {
        $this->semester = $semester;
        
        $this->mainFolders = ['Test Administration', 'Classroom Management', 'Syllabus Preparation'];
        
        $allFolders = FolderName::orderBy('main_folder_name')
            ->orderBy('folder_name')
            ->get();
        
        $this->subFolders = [];
        foreach ($this->mainFolders as $mainFolder) {
            $this->subFolders[$mainFolder] = $allFolders
                ->where('main_folder_name', $mainFolder)
                ->pluck('folder_name', 'folder_name_id')
                ->toArray();
        }
    }
    
    
    public function collection()
    {
        $facultyMembers = $this->getFacultyWithSubmissions();
        
        Log::info('Number of faculty members with submissions: ' . $facultyMembers->count());
        
        $data = collect();
        
        foreach ($facultyMembers as $index => $faculty) {
            Log::info('Processing faculty member: ' . $faculty->first_name . ' ' . $faculty->surname);
            
            $rowData = [
                'no' => $index + 1,
                'date_submitted' => $this->getLatestSubmissionDate($faculty->user_login_id),
                'faculty_name' => $faculty->first_name . ' ' . $faculty->surname,
            ];
            
            foreach ($this->mainFolders as $mainFolder) {
                foreach ($this->subFolders[$mainFolder] as $folderNameId => $folderName) {
                    $fileCount = CoursesFile::where('folder_name_id', $folderNameId)
                        ->where('user_login_id', $faculty->user_login_id)
                        ->where('semester', $this->semester)
                        ->count();
                
                    $rowData[$folderName] = $fileCount > 0 ? $fileCount : 'X';
                    Log::info("Faculty: {$faculty->surname}, Folder: {$folderName}, Count: {$rowData[$folderName]}");
                }
            }
            
            $rowData['semester'] = $this->semester;
            
            $data->push($rowData);
            Log::info('Added row data for faculty: ' . $faculty->surname);
        }
        
        Log::info('Number of rows in final data collection: ' . $data->count());
        Log::info('Data collection: ' . json_encode($data));
        
        return $data;
    }
    

    public function headings(): array
    {
        $headers = ['No.', 'Date Submitted', 'Faculty Name'];

        foreach ($this->mainFolders as $mainFolder) {
            foreach ($this->subFolders[$mainFolder] as $folderName) {
                $headers[] = $folderName;
            }
        }

        $headers[] = 'Semester';

        return $headers;
    }

    public function map($row): array
    {
        Log::info('Mapping row: ' . json_encode($row));

        $mappedRow = [
            $row['no'],
            $row['date_submitted'],
            $row['faculty_name']
        ];

        foreach ($this->mainFolders as $mainFolder) {
            foreach ($this->subFolders[$mainFolder] as $folderNameId => $subFolderName) {
                $value = $row[$subFolderName] ?? 'X';
                $mappedRow[] = $value;
                Log::info("Mapping folder: {$subFolderName}, Value: {$value}");
            }
        }

        $mappedRow[] = $row['semester'];

        Log::info('Mapped row: ' . json_encode($mappedRow));

        return $mappedRow;
    }


    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();
        
        Log::info("Styling sheet. Highest column: {$lastColumn}, Highest row: {$lastRow}");
        
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
            
            if (ord($endCol) >= ord($startCol)) {
                $sheet->setCellValue("{$startCol}1", strtoupper($mainFolder));
                $sheet->mergeCells("{$startCol}1:{$endCol}1");
                $sheet->getStyle("{$startCol}1:{$endCol}1")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $folderColors[$mainFolder]]],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                ]);
            }
            
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
        
        $data = $this->collection();
        $sheet->fromArray($data->map(function ($item) {
            return $this->map($item);
        })->toArray(), null, 'A3');
        
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A3:{$highestColumn}{$highestRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        for ($row = 3; $row <= $lastRow; $row++) {
            $cellValue = $sheet->getCell("C{$row}")->getValue();
            Log::info("Row {$row}, Faculty Name: {$cellValue}");
        }
        
        return [];
    }
    
    private function getFacultyWithSubmissions()
    {
        $query = UserLogin::where('role', 'faculty')
            ->whereHas('coursesFiles', function ($query) {
                $query->where('semester', $this->semester);
            })
            ->orderBy('surname')
            ->orderBy('first_name');

        $faculty = $query->get();
        
        return $faculty;
    }

    private function getLatestSubmissionDate($facultyId)
    {
        $latestFile = CoursesFile::where('user_login_id', $facultyId)
            ->where('semester', $this->semester)
            ->latest('created_at')
            ->first();

        return $latestFile ? Carbon::parse($latestFile->created_at)->setTimezone('Asia/Manila')->format('F d, Y, h:i A') : 'N/A';
    }
}