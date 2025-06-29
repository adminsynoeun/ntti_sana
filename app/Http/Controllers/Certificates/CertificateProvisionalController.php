<?php

namespace App\Http\Controllers\Certificates;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\StudentModel;
use Illuminate\Http\Request;
use App\Models\General\Skills;
use App\Models\General\Classes;
use App\Models\General\Sections;
use Illuminate\Support\Facades\DB;
use App\Models\General\SessionYear;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use App\Models\CertificateSubModule;
use App\Models\General\AssingClasses;
use App\Models\General\Qualifications;
use App\Models\SystemSetup\Department;
use App\Models\Certificates\CertStudentProvisional;
use App\Models\Certificates\CertStudentOfficialTranscriptCode;

class CertificateProvisionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $module_code = $request->module_code;

        $record_dept = Department::where('is_active', 'Yes')->get();
        $record_shift = Sections::where('is_active', 'Yes')->get();
        $arr_module = CertificateSubModule::where('code', $module_code)->get();

        $record_class = Classes::select('code', 'name')
            ->distinct()
            ->get();

        $record_level = Qualifications::get();

        $record_skill = Skills::whereHas('classes')->get();

        return view('certificate.provisional.provisional', [
            'record_class'  => $record_class,
            'record_dept'   => $record_dept,
            'record_shift'  => $record_shift,
            'module_code'   => $module_code,
            'arr_module'    => $arr_module,
            'record_level'  => $record_level,
            'record_skill'  => $record_skill,
        ])->render();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $dept_code = $request->input('dept_code');
        $class_code = $request->input('class_code');
        $qualification = $request->input('qualification');
        $sections_code = $request->input('sections_code');
        $skills_code = $request->input('skills_code');

        $search = $request->input('search');
        $rows_per_page = $request->input('rows_per_page', 50);

        $students = StudentModel::query()
            ->select([
                'student.*',
                'dept.name_2 as dept',
                'cls.name as class',
                'cls.level as level',
                'sk.name_2 as skill',
                DB::raw('(SELECT stu.name_2 FROM sections AS stu WHERE stu.code = cls.sections_code LIMIT 1) as section_type'),
                DB::raw('(SELECT pic.picture_ori FROM picture AS pic WHERE pic.code = student.code ORDER BY pic.id DESC LIMIT 1) as stu_photo'),
            ])
            ->join('department as dept', 'dept.code', '=', 'student.department_code')
            ->join('classes as cls', 'cls.code', '=', 'student.class_code')
            ->join('skills as sk', 'sk.code', '=', 'cls.skills_code')
            ->whereNotNull('student.department_code')
            ->whereNotNull('student.class_code')
            ->when($dept_code, function ($query, $dept_code) {
                if ($dept_code != 'all') {
                    $query->where('cls.department_code', $dept_code);
                }
            })
            ->when($class_code, function ($query, $class_code) {
                if ($class_code != 'all') {
                    $query->where('student.class_code', $class_code);
                }
            })
            ->when($qualification, function ($query, $qualification) {
                if ($qualification != 'all') {
                    $query->where('student.qualification', $qualification);
                }
            })
            ->when($sections_code, function ($query, $sections_code) {
                if ($sections_code != 'all') {
                    $query->where('cls.sections_code', $sections_code);
                }
            })
            ->when($skills_code, function ($query, $skills_code) {
                if ($skills_code != 'all') {
                    $query->where('cls.skills_code', $skills_code);
                }
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student.code', 'LIKE', "%{$search}%")
                        ->orWhere('student.name', 'LIKE', "%{$search}%")
                        ->orWhere('student.name_2', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('student.name_2', 'asc')
            ->paginate($rows_per_page);

        $students->getCollection()->transform(function ($student) {
            $student->stu_photo = DB::table('picture')
                ->where('code', $student->code)
                ->orderByDesc('id')
                ->value('picture_ori');

            $filePath = public_path('uploads/student/' . $student->stu_photo);

            $student->photo_status = $student->stu_photo && file_exists($filePath) ? true : false;

            $encrypted = secured_encrypt($student->code);
            $student->keyword = $encrypted;

            $record_assign_no = AssingClasses::where('class_code', $student->class_code)
                ->orderByDesc('years')
                ->orderByDesc('semester')
                ->first();
            $student->record_assign_no = $record_assign_no;

            $record_print = CertStudentProvisional::where('stu_code', $student->code)
                ->where('class_code', $student->class_code)
                ->orderByDesc('id')
                ->first();
            $student->record_print = $record_print;

            return $student;
        });

        return response()->json([
            'data' => $students->items(),
            'current_page' => $students->currentPage(),
            'last_page' => $students->lastPage(),
            'page' => $students->perPage(),
            'links' => $students->links('pagination::pagination-synoeun')->toHtml()
        ]);
    }

    public function showPrint(Request $request)
    {
        $key = $request->key;

        $stu_code = secured_decrypt($key);

        $record = CertStudentProvisional::where('stu_code', $stu_code)->orderBy('id', 'desc')->first();
        $records_info = StudentModel::where('code', $stu_code)
            ->select('name', 'name_2', 'code', 'gender', 'date_of_birth', 'qualification')
            ->orderBy('id', 'desc')
            ->first();

        $data = CertStudentOfficialTranscriptCode::with(
            [
                'qualification:code,name,name_3',
                'skill:code,name,name_2'
            ]
        )->where('active', 1)->where('code', $record->off_code ?? '')
            ->get()
            ->map(function ($item) {
                return [
                    'code' => $item->code,
                    'level_eng' => $item->qualification->name ?? '',
                    'level_kh' => $item->qualification->name_3 ?? '',
                    'skill_eng' => $item->skill->name ?? '',
                    'skill_kh' => $item->skill->name_2 ?? '',
                    'full_name' => ($item->qualification->name ?? 'N/A') . ' Of ' . ($item->skill->name ?? 'N/A'),
                ];
            });


        $subjects = DB::table('assing_classes_student_line as ass')
            ->join('assing_classes as acl', 'acl.assing_no', '=', 'ass.assing_line_no')
            ->join('subjects as su', 'su.code', '=', 'acl.subjects_code')
            ->where('ass.student_code', $stu_code)
            ->select(
                'acl.years',
                'acl.semester',
                'su.name_2 as sub_kh',
                'su.name as sub_eng',
                DB::raw('(IFNULL(SUM(ass.final), 0) + IFNULL(SUM(ass.attendance), 0) + IFNULL(SUM(ass.assessment), 0) + IFNULL(SUM(ass.midterm), 0)) AS score')
            )
            ->groupBy('acl.assing_no', 'acl.years', 'acl.semester', 'su.name_2', 'su.name')
            ->orderBy('acl.years', 'asc')
            ->orderBy('acl.semester', 'asc')
            ->get();



        $record_print = CertStudentProvisional::where('stu_code', $stu_code)
            ->select('print_date')
            ->orderByDesc('id')
            ->first();
        $groupedSubjects = $subjects->groupBy(['years', 'semester']);

        $html = view('certificate.provisional.print', [
            'stu_code'  => $stu_code,
            'record'  => $record ?? [],
            'records_info'  => $records_info,
            'records_transcript'  => $data,
            'records_subjects'  => $groupedSubjects,
            'record_print'  => $record_print,
        ])->render();

        $filePath = storage_path('app/public/print/output.pdf');
        $filename = 'YTM-' . Carbon::now()->format('Y-m-d') . '-' . Str::random(8) . '.pdf';
        Browsershot::html($html)
            ->setChromePath('C:\Users\DEV-404\.cache\puppeteer\chrome\win64-137.0.7151.55\chrome-win64\chrome.exe')
            ->setCustomTempPath(storage_path('app/private/livewire-tmp'))
            ->format('A4')
            ->margins(10, 5, 15, 5)
            ->landscape(false)
            ->showBackground()
            ->savePdf($filePath);
        return response()->stream(function () use ($filePath) {
            readfile($filePath);
            unlink($filePath);
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
