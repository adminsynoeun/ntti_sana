<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\General\AssingClasses;
use App\Models\General\Classes;
use App\Models\General\SanaHeader;
use App\Models\General\SanaLine;
use App\Models\General\Skills;
use App\Models\General\Subjects;
use App\Models\General\Teachers;
use App\Models\SystemSetup\Department;
use App\Service\service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentSanaController extends Controller
{
    //
    public $services;
    public $page_id;
    public $page;
    public $prefix;
    public $table_id;
    public $arrayJoin = [];

    function __construct()
    {
        $this->services = new service();
        $this->page_id = "10001";
        $this->page = "sana";
        $this->prefix = "sana";
        $this->arrayJoin = ['10001', '10007', '10008'];
        $this->table_id = "10005";
    }
    public function index(){
        $page = $this->page;
        $records = SanaHeader::with(['class.department', 'class.skill'])->orderBy('no', 'asc')->paginate(10);
        if(!Auth::check()){
            return redirect("login")->withSuccess('Opps! You do not have access');
        }  
        return view('general.student_sana', compact('records','page'));	
    }
    public function transaction(request $request)
    {
        $data = $request->all();
        $type = $data['type'];
        $page = $this->page;
        $page_url = $this->page;
        $records = null;
        $school_years = DB::table('session_year')->get();   
        $skills = DB::table('skills')->get();
        $departments = Department::get();
        $classs = Classes::get();
        $teachers = DB::table('teachers')->get();
          
        
        try {
            $params = ['records', 'type', 'page', 'skills', 'departments', 'classs', 'record_sub_lines', 'teachers', 'students'];
            if ($type == 'cr') return view('general.student_sana_card', compact($params));
            if (isset($_GET['code'])) {
                $records = SanaHeader::where('no', $this->services->Decr_string($_GET['code']))->first();
                $record_sub_lines = SanaLine::where('line_no', $records->no)->get();
                $students  = DB::table('student')->where('class_code', $records->class_code)->get();

                // dd($students);
            }
            return view('general.student_sana_card', compact($params));
        } catch (\Exception $ex) {
            $this->services->telegram($ex->getMessage(), $this->page, $ex->getLine());
            return response()->json(['status' => 'warning', 'msg' => $ex->getMessage()]);
        }
    }
    public function delete(Request $request)
    {
        $code = $request->code;
        try {
            $records = Skills::where('code',$code);
            $records->delete();
            DB::commit();
            return response()->json(['status' => 'success', 'msg' => 'ទិន្ន័យត្រូវបាន លុប​!']);
        } catch (\Exception $ex) {
            $this->services->telegram($ex->getMessage(), $this->page, $ex->getLine());
            return response()->json(['status' => 'warning', 'msg' => $ex->getMessage()]);
        }
    }
    public function update(Request $request)
    {
        $input = $request->all();
        $record = Skills::where('code', $input['code'])->first();
        if (!$record) return response()->json(['status' => 'error', 'msg' => "មិនអាចកែប្រ លេខកូដ!"]);
        $code = $input['type'];
        try {
            $records = Skills::where('code', $code)->first();
            if ($records) {
                $records->code = $request->code;
                $records->name = $request->name;
                $records->name_2 = $request->name_2;
                $records->department_code = $request->department_code;
                $records->status = $request->status;
                $records->update();
            }
            return response()->json(['status' => 'success', 'msg' => 'បច្ចុប្បន្នភាព ទិន្នន័យជោគជ័យ!', '$records' => $records]);
        } catch (\Exception $ex) {
            $this->services->telegram($ex->getMessage(), $this->page, $ex->getLine());
            return response()->json(['status' => 'warning', 'msg' => $ex->getMessage()]);
        }
    }
    public function store(request $request)
    {
        $input = $request->all();
        if (!$input['code']) {
            return response()->json(['status' => 'error', 'msg' => 'Field request form server!']);
        }
        $record = Skills::where('code', $request->code)->first();
        if ($record) return response()->json(['status' => 'error', 'msg' => "$request->code.កូដមានរូចហើយ​ !"]);
        try {
            $records = new Skills();
            $records->code = $request->code;
            $records->name = $request->name;
            $records->name_2 = $request->name_2;
            $records->department_code = $request->department_code;
            $records->status = $request->status;
            $records->save();
            return response()->json(['store' => 'yes', 'msg' => 'ទិន្នន័យ បន្ថែមជោគជ័យ!']);
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->services->telegram($ex->getMessage(), $this->page, $ex->getLine());
            return response()->json(['status' => 'warning', 'msg' => $ex->getMessage()]);
        }
    }
    public function Print(Request $request)
    {
        $data = $request->all();
        return dd($data);
        $class_record = null;
        $extract_query = $this->services->extractQuery($data);
        try {

            $records = Department::whereRaw($extract_query)->get();
            return view('student.student_print', compact('records', 'class_record'));
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->services->telegram($ex->getMessage(), $this->page, $ex->getLine());
            return response()->json(['status' => 'warning', 'msg' => $ex->getMessage()]);
        }
    }

    public function Search (Request $request,$page)
    {
        dd("helo");
        $input = $request->all();
        $strings = explode(" ", strtoupper($input['string']));
        $search_value = '';
        $user = Auth::user();
        if (count($strings) > 0) {
            if ($strings[0] == 'NEW' || $strings[0] == 'OPEN') {
                if (count($strings) > 2) {
                    for ($i = 1; $i < count($strings) - 1; $i++) {
                        $search_value .= $strings[$i] . " ";
                    }
                } else {
                    if (count($strings) == 2) {
                        $search_value = $strings[1];
                    }
                }
                $search_value = rtrim($search_value, " ");
                // check page
                if($page == 'student'){
                    $menus = DB::table('student')->where('name','like', $search_value . "%")
                                        ->orWhere('code', 'like', $search_value . "%")
                                        ->orWhere('name_2', 'like', $search_value . "%")
                                        ->where('class_code', '<>', null)->get();
                    $blade_file_record = 'student.student_list';
                }else if($page == 'department'){
                    $menus = DB::table('department')->where('department_name','like', $search_value . "%")
                                        ->where('id', '<>', null)->get();
                    $blade_file_record = 'department.department_list';
                }

                if (count($menus) > 0) {
                    foreach ($menus as $menu) {
                        if ($strings[0] == 'OPEN' && count($strings) > 2) {
                            $menu->code = $menu->code . ' ' . $strings[count($strings) - 1];
                        }
                        $menu->url = $menu->url . ($strings[0] == 'NEW' ? "type=cr" : "type=ed&code=" . $this->service->Encr_string($strings[count($strings) - 1]));
                    }
                }
            }else{
                for ($i = 0; $i < count($strings); $i++) {
                    $search_value .= $strings[$i] . " ";
                }
                $search_value = rtrim($search_value, " ");
                if($page == 'student'){
                    $menus = DB::table('student')->where('name', 'like', $search_value . "%")
                        ->orWhere('code', 'like', $search_value . "%")
                        ->orWhere('name_2', 'like', $search_value . "%")
                        ->where('class_code', '<>', null)->paginate(1000);
                    $blade_file_record = 'student.student_list';
                }else if($page == 'department'){
                    $menus = DB::table('department')->where('department_name','like', $search_value . "%")
                            ->where('id', '<>', null)->paginate(1000);
                    $blade_file_record = 'department.department_list';
                }
            }
           
            if (count($menus) > 0) {
                $records = $menus;
            }else{
                if($page == 'student'){
                    $records = Student::where('department_code',$user->childs)->paginate(10);
                }else if($page == 'department'){
                    $records = Department::paginate(15);
                }
            }
            $view = view($blade_file_record,compact('records'))->render();
            return response()->json(['status' =>'success','view' =>$view]);
        }
        return 'none';
    }

    public function EcitStudentTransactionSana(Request $request)
    {
        $data = $request->all();
        try {

            $records = SanaLine::where('sub_class_code',  $data['id'])->where('group', 'Yes')->first();
            $teachers = Teachers::get();
            $teachers_name = Teachers::where('code', $records->teacher_leader_code)->value('name_2');
            $teachers_code = Teachers::where('code', $records->teacher_leader_code)->value('code');

            $storedData = $records->teacher_consult_code;
            
            $storedDataArray = explode(",", $storedData); // Convert to array
            $storedDataName = Teachers::whereIn('code', $storedDataArray)->pluck('name_2')->toArray();

            $storedDataNameString = implode(", ", $storedDataName);
        
            return response()->json(['status' => 'success', 'records' => $records, 'teachers' => $teachers, 'teachers_name' => $teachers_name, 'teachers_code' => $teachers_code, 'storedData'=> $storedData , 'storedDataNameString'=> $storedDataNameString ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->services->telegram($ex->getMessage(), $this->page, $ex->getLine());
            return response()->json(['status' => 'warning', 'msg' => $ex->getMessage()]);
        }
    }

    public function EcitStudentSana(Request $request)
    {
        $data = $request->all();

        try {
            $records = SanaLine::where('id',  $data['id'])->where('group', 'No')->first();
            $students_name = $records->student->name_2;
            return response()->json(['status' => 'success', 'records' => $records, 'students_name' => $students_name]);
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->services->telegram($ex->getMessage(), $this->page, $ex->getLine());
            return response()->json(['status' => 'warning', 'msg' => $ex->getMessage()]);
        }
    }

    public function SaveStudentSana(Request $request)
    {
        $data = $request->all();

        dd($data);
        
        try {
            $records = SanaLine::where('id',  $data['id'])->where('group', 'No')->first();
            $students_name = $records->student->name_2;
            return response()->json(['status' => 'success', 'records' => $records, 'students_name' => $students_name]);
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->services->telegram($ex->getMessage(), $this->page, $ex->getLine());
            return response()->json(['status' => 'warning', 'msg' => $ex->getMessage()]);
        }
    }
    
}
