<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Http\Requests\StoreRecordRequest;
use App\Http\Requests\UpdateRecordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller{
    public function index(){ return Record::all(); }
    public function show(Record $record){ return $record; }
    public function history(Request $request){
        return Record::where('date', '>=', $request->dateIni)
            ->where('date', '<=', $request->dateEnd)
            ->get();
    }
    public function query(Request $request){
        $asistencias = DB::select("
            SELECT date,count(*) as cantidad FROM records WHERE date >= ? AND date <= ? GROUP BY date",
            [$request->dateIni, $request->dateEnd]
        );
        $schedule = DB::select("
            SELECT schedule,count(*) as cantidad FROM records WHERE date >= ? AND date <= ? GROUP BY schedule",
            [$request->dateIni, $request->dateEnd]
        );
        return [
            'asistencias' => $asistencias,
            'schedule' => $schedule
        ];
    }
    public function store(Request $request){

        return Record::create($request->all());
    }
    public function update(Request $request, Record $record){ $record->update($request->all()); return $record; }
    public function destroy(Record $record){ $record->delete(); return $record; }
}
