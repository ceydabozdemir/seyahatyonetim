<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter; // Correct usage of Filter
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index()
    {
        $records = Record::all();
        return view('records.index', compact('records'));
    }

    public function table(Table $table)
    {
        return $table
            ->filters([
                Filter::make('name')
                    ->label('Name Filter')
                    ->query(function ($query, $value) {
                        return $query->where('name', 'like', "%{$value}%");
                    }),
            ]);
    }

    public function create()
    {
        return view('records.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        Record::create($request->all());
        return redirect()->route('records.index');
    }

    public function edit($id)
    {
        $record = Record::findOrFail($id);
        return view('records.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $record = Record::findOrFail($id);
        $record->update($request->all());
        return redirect()->route('records.index');
    }

    public function destroy($id)
    {
        Record::destroy($id);
        return redirect()->route('records.index');
    }
}
