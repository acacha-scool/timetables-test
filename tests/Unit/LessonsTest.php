<?php

namespace Tests\Unit;

use Scool\EbreEscoolModel\AcademicPeriod;
use Scool\EbreEscoolModel\ClassroomGroup;
use Scool\EbreEscoolModel\Lesson;
use Tests\TestCase;

use Dotenv\Dotenv;


/**
 * Class LessonsTest.
 *
 * @package Tests\Unit
 */
class LessonsTest extends TestCase
{

    /**
     * EbreEscoolDatabaseTest constructor.
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->bootEloquent();
    }

    /**
     * Boot eloquent.
     */
    public function bootEloquent()
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $dotenv = new Dotenv(__DIR__ . '/../..');
        $dotenv->load();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => env('TUNNELER_LOCAL_ADDRESS', 'localhost'),
            'port' => env('TUNNELER_LOCAL_PORT', '3306'),

            'database' => env('MIGRATION_RO_DB_DATABASE', 'ebre_escool'),
            'username' => env('MIGRATION_RO_DB_USERNAME', 'sergi'),
            'password' => env('MIGRATION_RO_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ], 'ebre_escool');

        $capsule->bootEloquent();
    }

    /**
     * TEst current academic period exists and have correct_values
     * @return void
     */
    public function testCurrentAcademicPeriodExistsAndHaveCorrectValues()
    {
        // Test current academic period is 2017-18
        $currentAP = AcademicPeriod::current();

        $this->assertTrue($currentAP->count() === 1);

        $this->assertTrue($currentAP->first()->shortname === "2017-18");
        $this->assertTrue($currentAP->first()->name === "2017-2018");
        $this->assertTrue($currentAP->first()->name === "2017-2018");
        $this->assertTrue($currentAP->first()->alt_name === "201718");
        $this->assertTrue($currentAP->first()->initial_date === "2017-09-01");
        $this->assertTrue($currentAP->first()->final_date === "2018-07-31");
    }

    /**
     *
     *
     * @return void
     */
    public function testThereIsAtLeastOneLessonForCurrentAcademicPeriod()
    {
        $lessons = Lesson::active();
        $this->assertTrue($lessons->count() > 1);
    }

    /**
     *
     * @return void
     */
    public function testThereIsAtLeastMoreThanANormalNumberOfLessonsForCurrentAcademicPeriod()
    {
        $lessons = Lesson::active();
        $this->assertTrue($lessons->count() > 1000);
    }

    // GRUPS DE CLASSE

    /**
     * @group failing
     * @return void
     */
    public function testThereAreAnExceptedNumberOfClassroomGroupsInLessons()
    {
        //SELECT * FROM ebre_escool.lesson WHERE lesson.lesson_academic_period_id = 8;

        $classrooms = ClassroomGroup::whereHas('periods', function ($query) {
            $query->where('academic_periods_current', '=', 1);
        })->get();

        foreach ($classrooms as $classroom) {
            dump($classroom->code);
        }
        dump('finished!');
    }

    /**
     * @group caca
     * @dataProvider activeClassrooms
     */
    public function testThereIsAtLeastOneLessonAssignedToClassroomgroup($classroom)
    {
        dd($classroom);
        $lessons = Lesson::where('lesson_classroom_group_id', $classroom->id);
        $this->assertTrue($lessons->count() > 1000);
//        => Scool\EbreEscoolModel\Lesson {#17524
//        lesson_id: 1,
//     lesson_academic_period_id: 4,
//     lesson_import_id: 0,
//     lesson_code: "2450",
//     lesson_codi_assignatura: "C1",
//     lesson_classroom_group_id: 18,
//     lesson_codi_grup: "1PRP",
//     lesson_teacher_id: 11,
//     lesson_codi_professor: "09",
//     lesson_study_module_id: 1,
//     lesson_location_id: 0,
//     codi_espai: "",
//     lesson_day: 1,
//     lesson_time_slot_id: 13,
//     codi_hora: 10,
//     lesson_entryDate: "0000-00-00 00:00:00",
//     lesson_last_update: "2014-12-12 07:45:53",
//     lesson_creationUserId: 3292,
//     lesson_lastupdateUserId: 3292,
//     lesson_markedForDeletion: "n",
//     lesson_markedForDeletionDate: "0000-00-00 00:00:00",
//   }

    }

    /**
     * Get active classrooms.
     */
    public function activeClassrooms()
    {
//        dd(AcademicPeriod::current()->first()->classrooms);
        $activeClassrooms = [];
        foreach (AcademicPeriod::current()->first()->classrooms as $classroom) {
            $activeClassrooms[$classroom->code] = [
                $classroom
            ];
        }
        return $activeClassrooms;
    }

    //    /**
//     * Teachers provider by academic period.
//     */
//    public function teachersTotalsProviderByAcademicPeriod()
//    {
//        $teachersByAP = [];
//        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
//            $currentTeachers = Teacher::activeOn($academicPeriod->id)->get();
//            $teachersByAP[$academicPeriod->name] = [
//                $currentTeachers->count(),
//                $academicPeriod->id
//            ];
//        }
//        return $teachersByAP;
//
//    }

}

