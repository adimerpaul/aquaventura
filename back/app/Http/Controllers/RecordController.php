<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Http\Requests\StoreRecordRequest;
use App\Http\Requests\UpdateRecordRequest;

class RecordController extends Controller{
    public function index(){ return Record::all(); }
    public function show(Record $record){ return $record; }
    public function store(StoreRecordRequest $request){ return Record::create($request->all()); }
    public function update(UpdateRecordRequest $request, Record $record){ $record->update($request->all()); return $record; }
    public function destroy(Record $record){ $record->delete(); return $record; }
}
