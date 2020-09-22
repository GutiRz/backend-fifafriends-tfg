<?php

namespace App\Http\Controllers;

use App\Team;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    public function index() {
        return Team::all();
    }

    public function show(Team $team){
        return $team->load('players');
    }

    public function store(Request $request){
        return Team::create([
            'name' => $request->name,
        ]);
    }

    public function update(Request $request, Team $team){
        return $team->update([
            'name' => $request->name
        ]);
    }

    public function destroy(Team $team) {
        try {
            return $team->delete();
        } catch (\Exception $e) {
            return abort(404);
        }
    }
}
