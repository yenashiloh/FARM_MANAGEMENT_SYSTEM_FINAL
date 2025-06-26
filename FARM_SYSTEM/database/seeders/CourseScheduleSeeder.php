<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UserLogin;

class CourseScheduleSeeder extends Seeder
{
    public function run()
    {
        $userIds = UserLogin::whereIn('user_login_id', [2, 4, 5, 6, 3])->pluck('user_login_id')->toArray();

        if (count($userIds) < 5) {
            $this->command->error('User IDs 2, 3, 4, 5, 6 are not present in the database.');
            return;
        }

        DB::table('course_schedules')->insert([
            [
                'course_schedule_id' => 1,
                'user_login_id' => $userIds[0],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Information Technology (BSIT)',
                'course_code' => 'CS101',
                'course_subjects' => 'Introduction to Computer Science',
                'year_section' => '1-1',
                'schedule' => 'Mon 10:00 - 12:00, Wed 10:00 - 12:00',
            ],
            [
                'course_schedule_id' => 2,
                'user_login_id' => $userIds[0],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Information Technology (BSIT)',
                'course_code' => 'DS201',
                'course_subjects' => 'Data Structures',
                'year_section' => '1-2',
                'schedule' => 'Tues 11:00 - 13:00, Thurs 11:00 - 13:00',
            ],
            [
                'course_schedule_id' => 3,
                'user_login_id' => $userIds[0],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Diploma in Information Communication Technology (DICT)',
                'course_code' => 'DS202',
                'course_subjects' => 'Database Systems',
                'year_section' => '2-2',
                'schedule' => 'Wed 14:00 - 16:00, Fri 10:00 - 12:00',
            ],
            [
                'course_schedule_id' => 4,
                'user_login_id' => $userIds[0],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Diploma in Information Communication Technology (DICT)',
                'course_code' => 'OS102',
                'course_subjects' => 'Operating Systems',
                'year_section' => '2-1',
                'schedule' => 'Mon 09:00 - 11:00, Thurs 09:00 - 11:00',
            ],
            [
                'course_schedule_id' => 5,
                'user_login_id' => $userIds[0],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Applied Mathematics (BSAM)',
                'course_code' => 'MGT101',
                'course_subjects' => 'Principles of Management and Organization',
                'year_section' => '3-1',
                'schedule' => 'Tues 13:00 - 15:00, Fri 13:00 - 15:00',
            ],
        ]);

        DB::table('course_schedules')->insert([
            [
                'course_schedule_id' => 6,
                'user_login_id' => $userIds[1],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Entrepreneurship (BSENTREP)',
                'course_code' => 'ENT301',
                'course_subjects' => 'Technopreneurship',
                'year_section' => '1-1',
                'schedule' => 'Mon 09:00 - 11:00, Wed 09:00 - 11:00',
            ],
            [
                'course_schedule_id' => 7,
                'user_login_id' => $userIds[1],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Diploma in Information Communication Technology (DICT)',
                'course_code' => 'CLS1',
                'course_subjects' => 'Calculus I',
                'year_section' => '1-3',
                'schedule' => 'Tues 10:00 - 12:00, Thurs 10:00 - 12:00',
            ],
            [
                'course_schedule_id' => 8,
                'user_login_id' => $userIds[1],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Applied Mathematics (BSAM)',
                'course_code' => 'MTH203',
                'course_subjects' => 'Statistics',
                'year_section' => '3-2',
                'schedule' => 'Wed 11:00 - 13:00, Fri 11:00 - 13:00',
            ],
            [
                'course_schedule_id' => 9,
                'user_login_id' => $userIds[1],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Information Technology (BSIT)',
                'course_code' => 'DB301',
                'course_subjects' => 'Discrete Mathematics',
                'year_section' => '2-2',
                'schedule' => 'Mon 12:00 - 14:00, Thurs 12:00 - 14:00',
            ],
            [
                'course_schedule_id' => 10,
                'user_login_id' => $userIds[1],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Information Technology (BSIT)',
                'course_code' => 'SYS202',
                'course_subjects' => 'Systems Analysis and Design',
                'year_section' => '3-1',
                'schedule' => 'Tues 14:00 - 16:00, Fri 14:00 - 16:00',
            ],
        ]);

        DB::table('course_schedules')->insert([
            [
                'course_schedule_id' => 11,
                'user_login_id' => $userIds[2],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Entrepreneurship (BSENTREP)',
                'course_code' => 'ENT301',
                'course_subjects' => 'Technopreneurship',
                'year_section' => '1-1',
                'schedule' => 'Mon 09:00 - 11:00, Wed 09:00 - 11:00',
            ],
            [
                'course_schedule_id' => 12,
                'user_login_id' => $userIds[2],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Applied Mathematics (BSAM)',
                'course_code' => 'MGT101',
                'course_subjects' => 'Principles of Management and Organization',
                'year_section' => '3-1',
                'schedule' => 'Tues 13:00 - 15:00, Fri 13:00 - 15:00',
            ],
            [
                'course_schedule_id' => 13,
                'user_login_id' => $userIds[2],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Applied Mathematics (BSAM)',
                'course_code' => 'MTH203',
                'course_subjects' => 'Statistics',
                'year_section' => '3-2',
                'schedule' => 'Wed 11:00 - 13:00, Fri 11:00 - 13:00',
            ],
            [
                'course_schedule_id' => 14,
                'user_login_id' => $userIds[2],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Information Technology (BSIT)',
                'course_code' => 'DB301',
                'course_subjects' => 'Discrete Mathematics',
                'year_section' => '2-2',
                'schedule' => 'Mon 12:00 - 14:00, Thurs 12:00 - 14:00',
            ],
        ]);


        DB::table('course_schedules')->insert([
            [
                'course_schedule_id' => 15,
                'user_login_id' => $userIds[3],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Diploma in Information Communication Technology (DICT)',
                'course_code' => 'DS202',
                'course_subjects' => 'Database Systems',
                'year_section' => '2-2',
                'schedule' => 'Wed 14:00 - 16:00, Fri 10:00 - 12:00',
            ],
            [
                'course_schedule_id' => 16,
                'user_login_id' => $userIds[3],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Diploma in Information Communication Technology (DICT)',
                'course_code' => 'OS102',
                'course_subjects' => 'Operating Systems',
                'year_section' => '2-1',
                'schedule' => 'Mon 09:00 - 11:00, Thurs 09:00 - 11:00',
            ],
            [
                'course_schedule_id' => 17,
                'user_login_id' => $userIds[3],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Applied Mathematics (BSAM)',
                'course_code' => 'MGT101',
                'course_subjects' => 'Principles of Management and Organization',
                'year_section' => '3-1',
                'schedule' => 'Tues 13:00 - 15:00, Fri 13:00 - 15:00',
            ],
        ]);

        DB::table('course_schedules')->insert([
            [
                'course_schedule_id' => 18,
                'user_login_id' => $userIds[4],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Diploma in Information Communication Technology (DICT)',
                'course_code' => 'DS202',
                'course_subjects' => 'Database Systems',
                'year_section' => '2-2',
                'schedule' => 'Wed 14:00 - 16:00, Fri 10:00 - 12:00',
            ],
            [
                'course_schedule_id' => 19,
                'user_login_id' => $userIds[4],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Diploma in Information Communication Technology (DICT)',
                'course_code' => 'OS102',
                'course_subjects' => 'Operating Systems',
                'year_section' => '2-1',
                'schedule' => 'Mon 09:00 - 11:00, Thurs 09:00 - 11:00',
            ],
            [
                'course_schedule_id' => 20,
                'user_login_id' => $userIds[4],
                'sem_academic_year' => 'First Sem 2024-2025',
                'program' => 'Bachelor of Science in Applied Mathematics (BSAM)',
                'course_code' => 'MGT101',
                'course_subjects' => 'Principles of Management and Organization',
                'year_section' => '3-1',
                'schedule' => 'Tues 13:00 - 15:00, Fri 13:00 - 15:00',
            ],
        ]);
       
    }
}