<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RecordController extends Controller
{
    public function getRecords() {

        return response()->json([
            'info' => [
                'user_id' => Auth::user()->id,
                'count_records' => Auth::user()->records->count()
            ],
            'results' => Auth::user()->records
        ]);
    }

    public function getRecord($record_id) {
        $record = Record::where('id', $record_id)->first();

        if($record) {
            return response()->json([
                'info' => [
                    'title' => $record->title,
                    'body' => $record->body,
                ]
            ]);
        }

        return response()->json([
            'error' => [
                'code' => 404,
                'message' => 'Not found!'
            ]
        ],404);
    }

    public function addRecord(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'body' => 'required|max:10000'
        ]);

        if($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 400,
                    'message' => 'Validation error!',
                    'errors' => $validator->errors()
                ]
            ], 400);
        }

        Record::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => Auth::user()->id
        ]);
    }

    public function deleteRecord($record_id) {
        $record = Record::where('id', $record_id)->first();

        if($record) {
            $record->delete();
            return response()->json()->setStatusCode(200);
        }


        return response()->json([
            'error' => [
                'code' => 404,
                'message' => 'Not found!'
            ]
        ],404);
    }

    public function updateRecord($record_id, Request $request) {
        //
    }
}
