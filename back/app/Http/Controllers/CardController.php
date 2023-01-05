<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\Record;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        return Card::orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $existingCard = Card::where('code', $request->code)->first();
        if ($existingCard) {
            return response()->json(['message' => 'Tarjeta ya registrada'], 409);
        }
        $number = Card::whereDate('created_at', date('Y-m-d'))->count() + 1;
        $request['number'] = $number;
        $card = Card::create($request->all());
        return Card::find($card->id);
    }
    public function maxTarget()
    {
        $maxTarget = Card::max('codeTarget');
        return $maxTarget+1;
    }
    public function show($code){
        $card = Card::where('code', $code)->first();
        if(!$card){
            return response()->json(['message' => 'Tarjeta no encontrada'], 404);
        }
        if ( date('Y-m-d') > $card->dateEnd ){
            return response()->json(['message' => 'Tarjeta vencida'], 409);
        }
        if ( date('Y-m-d') < $card->dateIni ){
            return response()->json(['message' => 'Tarjeta no disponible'], 409);
        }
        $record = Record::where('code', $code)->where('date', date('Y-m-d'))->first();
        if($record){
            return response()->json(['message' => 'Tarjeta ya registrada'], 409);
        }
        $card = Card::where('code', $code)->first();
        $record = new Record();
        $record->ci=$card->ci;
        $record->code=$card->code;
        $record->dateIni=$card->dateIni;
        $record->dateEnd=$card->dateEnd;
        $record->codeTarget=$card->codeTarget;
        $record->name=$card->name;
        $record->birthday=$card->birthday;
        $record->phone=$card->phone;
        $record->schedule=$card->schedule;
        $record->amount=$card->amount;
        $record->type=$card->type;
        $record->observation='';
        $record->date=date('Y-m-d');
        $record->time=date('H:i:s');
        $record->card_id=$card->id;
        $record->save();
        return $card;
    }
    public function update(Request $request, Card $card){
        $valid= $request->validate([
            'code' => 'required|unique:cards,code,'.$card->id,
        ]);
        $card->update($request->all());
        return Card::find($card->id);
    }
    public function destroy(Card $card){
        return $card->delete();
    }

}
