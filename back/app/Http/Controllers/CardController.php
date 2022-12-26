<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use Illuminate\Http\Request;

class CardController extends Controller{
    public function index(){ return Card::orderBy('id', 'desc')->get(); }
    public function store(Request $request){
        $existingCard = Card::where('code', $request->code)->first();
        if($existingCard){
            return response()->json(['message' => 'Tarjeta ya registrada'], 409);
        }
        $card = Card::create($request->all());
        return Card::find($card->id);
    }
    public function show($code){ return Card::where('code', $code)->first(); }
    public function update(Request $request, Card $card){
        $existingCard = Card::where('code', $request->code)->first();
        if($existingCard){
            return response()->json(['message' => 'Tarjeta ya registrada'], 409);
        }
        $card->update($request->all());
        return Card::find($card->id);
    }
    public function destroy(Card $card){
        return $card->delete();
    }
}
