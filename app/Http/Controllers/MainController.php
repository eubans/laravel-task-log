<?php

namespace App\Http\Controllers;

use App\Models\Tasks;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {   
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $this->ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }

        $this->ip = '119.92.72.135'; //temporary
        // $this->ip = '104.131.191.209'; //temporary
        // $this->ip = '103.58.115.141'; //temporary

        $ipInfo = file_get_contents('http://ip-api.com/json/' . $this->ip);
        $ipInfo = json_decode($ipInfo);
        $this->timezone = 'UTC';
        if(isset($ipInfo->timezone)){
            $this->timezone = $ipInfo->timezone;
        }

        config(['app.timezone' => $this->timezone]);
    }
     
    public function index(Request $request)
    {   
        $taskList = Tasks::getAllTasks(auth()->user()->id);
        $dateTimeNow = Carbon::now()->setTimezone($this->timezone);
        
        return view('dashboard.index')
            ->with('taskList', $taskList)
            ->with('dateNow', $dateTimeNow->format('Y-m-d'))
            ->with('timeNow', $dateTimeNow->format('H:i'))
            ->with('timeZone', $this->timezone);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);

        try {
            $request->time = Carbon::parse($request->time)->format('H:i:s');

            $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->date . " " . $request->time, $this->timezone);
            $dateTime->setTimezone('Asia/Manila');

            $task = new Tasks;
            $task->title = $request->title;
            $task->date_time = $dateTime;
            $task->created_by = auth()->user()->id;

            if ($task->save()) {
                Session::put('SAVE_STATUS', 'success');
            }else{
                Session::put('SAVE_STATUS', 'failed');
            }
            return redirect()->to('dashboard');
        } catch (\Throwable $th) {
            Session::put('SAVE_STATUS', 'failed');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $task = Tasks::find($id);

            if ($task->delete()) {
                Session::put('SAVE_STATUS', 'success-delete');
            }else{
                Session::put('SAVE_STATUS', 'failed');
            }
            return redirect()->to('dashboard');
        } catch (\Throwable $th) {
            Session::put('SAVE_STATUS', 'failed');
        }
    }
}
