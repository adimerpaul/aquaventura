<?php

namespace App\Http\Controllers;

use App\Models\Card;
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
    public function queries(Request $request){
        $dias = array("domingo","lunes","martes","miércoles","jueves","viernes","sábado");
        $cards = DB::select("
            SELECT * FROM cards WHERE (dateIni between ? and ? OR dateEnd between ? and ?) AND schedule = ?",
            [$request->dateIni, $request->dateEnd, $request->dateIni, $request->dateEnd, $request->schedule]
        );
        foreach ($cards as $key => $card) {
            $auxDateIni = $card->dateIni;
            $records=[];
            while($auxDateIni <= $card->dateEnd){
                if ($card->days=="MARTES-VIERNES"){
                    if ($dias[date('w', strtotime($auxDateIni))]=="martes" || $dias[date('w', strtotime($auxDateIni))]=="miércoles" || $dias[date('w', strtotime($auxDateIni))]=="jueves" || $dias[date('w', strtotime($auxDateIni))]=="viernes"){
                        $record = DB::select("
                            SELECT * FROM records WHERE date = ? AND card_id = ?",
                            [$auxDateIni, $card->id]
                        );
                        $records[] = [
                            "date"=>$auxDateIni,
                            "datedmY"=>date('d-m-Y', strtotime($auxDateIni)),
                            "day"=>$dias[date('w', strtotime($auxDateIni))],
                            "record"=>$record==[]?false:true
                        ];
                    }
                }else if ($card->days=="LUNES-MIERCOLES"){
                    if ($dias[date('w', strtotime($auxDateIni))]=="lunes" || $dias[date('w', strtotime($auxDateIni))]=="miércoles"){
                        $record = DB::select("
                            SELECT * FROM records WHERE date = ? AND card_id = ?",
                            [$auxDateIni, $card->id]
                        );
                        $records[] = [
                            "date"=>$auxDateIni,
                            "datedmY"=>date('d-m-Y', strtotime($auxDateIni)),
                            "day"=>$dias[date('w', strtotime($auxDateIni))],
                            "record"=>$record==[]?false:true
                        ];
                    }
                }else if ($card->days=="MARTE-JUEVES"){
                    if ($dias[date('w', strtotime($auxDateIni))]=="martes" || $dias[date('w', strtotime($auxDateIni))]=="jueves"){
                        $record = DB::select("
                            SELECT * FROM records WHERE date = ? AND card_id = ?",
                            [$auxDateIni, $card->id]
                        );
                        $records[] = [
                            "date"=>$auxDateIni,
                            "datedmY"=>date('d-m-Y', strtotime($auxDateIni)),
                            "day"=>$dias[date('w', strtotime($auxDateIni))],
                            "record"=>$record==[]?false:true
                        ];
                    }
                }

                $auxDateIni = date('Y-m-d', strtotime($auxDateIni . ' + 1 days'));
            }
            $cards[$key]->records = $records;
        }
        return $cards;
    }
}
