<?php

namespace App\Http\Controllers;

use App\Http\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TreatmentController extends Controller
{    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function treatments(Request $request){
        $sort = $request->input('sort');
        if($request->input('filter_re_taking') === 'true')
            $re_taking = true;
        elseif($request->input('filter_re_taking') === 'false')
            $re_taking = false;

        if($request->input('filter_status') !== null && $request->input('filter_re_taking') !== null){
            $treatments = Auth::user()->treatments()->where(['status' => $request->input('filter_status'), 're_taking' => $re_taking])->orderBy($sort, 'asc')->paginate(15);
        } elseif($request->input('filter_status') !== null || $request->input('filter_re_taking') !== null){
            $treatments = Auth::user()->treatments()->where(isset($re_taking) ? ['re_taking' => $re_taking] : ['status' => $request->input('filter_status')])->orderBy($sort, 'asc')->paginate(15);
        } else{
            $treatments = Auth::user()->treatments()->orderBy($sort, 'asc')->paginate(15);
        }
        
        return view('treatments', ['treatments' => $treatments]);
    }

    public function addTreatment(Request $request, $id){
        $treatment = new Treatment;
        $treatment->course_id = $request->course;
        $treatment->patient_id = $id;
        $treatment->status = Treatment::STATUS['GOES'];
        $treatment->results = 'Курс розпочато.';
        $treatment->re_taking = false;
        $treatment->passed_days = 0;
        $treatment->save();
        return redirect()->route('patients', ['sort' => 'created_at']);
    }

    public function updateTreatment(Request $request, $id){
        $treatment = Treatment::find($id);
        $treatment->status = $request->status;
        $treatment->passed_days = $request->passed_days;
        $treatment->results = $request->results;
        if(isset($request->re_taking)){
            $treatment->re_taking = true;
        } else{
            $treatment->re_taking = false;
        }
        $treatment->save();

        return redirect()->back();
    }
}
