<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Prize;
use App\Http\Requests\PrizeRequest;
use App\Models\Simulate;
use Illuminate\Http\Request;



class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $prizes = Prize::all();
        // Calculate the total probability
        $totalProbability = Prize::sum('probability');
        //rewards for charts
        $rewards = Simulate::with('prize')->get();
        // Check if total probability equals 100
        if ($totalProbability == 100) {
            // Total probability equals 100, proceed with displaying the view
            return view('prizes.index', ['prizes' => $prizes, 'totalProbability' => $totalProbability, 'remainingProbability' => 0, 'rewards' => $rewards]);
        } else {
            // Total probability is below 100, calculate remaining probability
            $remainingProbability = 100 - $totalProbability;
            // Pass the remaining probability to the view along with prizes and total probability
            return view('prizes.index', [
                'prizes' => $prizes,
                'totalProbability' => $totalProbability,
                'remainingProbability' => $remainingProbability,
                'rewards' => $rewards
            ]);
        }
        // return view('prizes.index', ['prizes' => $prizes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('prizes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {
        $newProbability = floatval($request->input('probability'));

        if (Prize::validateProbability($newProbability)) {
            $prize = new Prize;
            $prize->title = $request->input('title');
            $prize->probability = $newProbability;
            $prize->save();
            return to_route('prizes.index');
        } else {
            return redirect()->back()->withErrors(['probability' => 'The total probability of all prizes cannot exceed 100%.'])->withInput();
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        return view('prizes.edit', ['prize' => $prize]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, $id)
    {
        $prize = Prize::findOrFail($id);
        $newProbability = floatval($request->input('probability'));

        $total = Prize::where('id', '!=', $id)->sum('probability');
        $updatedTotalProbability = $total + $newProbability;
        if ($updatedTotalProbability <= 100) {
            $prize->title = $request->input('title');
            $prize->probability = $newProbability;
            $prize->save();
            return to_route('prizes.index');
        } else {
            return redirect()->back()->withErrors(['probability' => 'The total probability of all prizes cannot exceed 100%.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        return to_route('prizes.index');
    }


    public function simulate(Request $request)
    {
        $number_of_prizes = $request->number_of_prizes;
        $prizes = Prize::all();

        foreach ($prizes as $prize) {
            $prize_id = $prize->id;
            $probability = $prize->probability / 100;
            $winner_number = round($number_of_prizes * $probability);
            $prize->awarded += $winner_number;
            $prize->save();

            $percentage_of_winners = ($winner_number / $number_of_prizes) * 100;
            $simulate = Simulate::where('prize_id', $prize_id)->first();

            if ($simulate) {
                // If a record for this prize already exists, update its percentage_of_winners
                $simulate->percentage_of_winners = $percentage_of_winners;
                $simulate->save();
            } else {
                // If no record exists for this prize, create a new one
                $simulate = new Simulate;
                $simulate->prize_id = $prize_id;
                $simulate->percentage_of_winners = $percentage_of_winners;
                $simulate->save();
            }
        }
        // Prize::nextPrize();
        // exit();

        return to_route('prizes.index');
    }

    public function reset()
    {
        Simulate::truncate();
        return to_route('prizes.index');
    }
}
