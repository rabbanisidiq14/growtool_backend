<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Portfolio;

class ProcessController extends Controller
{
    public function getAllLogs(){
        $logs = Log::orderBy('id', 'desc')->get();

        return response()->json($logs, 200);
    }

    public function buyItem(Request $request)
    {
        $isAlreadyHave = Portfolio::where('name', $request -> name)->first();
        $logs = Log::create([
            'type' => 'buy',
            'name' => $request -> name,
            'amount' => $request -> amount,
            'price' => $request -> price
        ]);
        if($isAlreadyHave){
            $amount = $isAlreadyHave -> amount + $request -> amount;
            $totalSpent = $isAlreadyHave -> amount * $isAlreadyHave -> initialPrice;
            $totalNow = $totalSpent + ($request -> amount * $request -> price);
            $initialPrice = $totalNow / $amount;

            $portfolio = Portfolio::where('name', $request -> name)
            ->update([
                'amount' => $isAlreadyHave->amount+$request->amount,
                'initialPrice' => $initialPrice
            ]);
        }else{
            $portfolio = Portfolio::create([
                'name' => $request -> name,
                'amount' => $request -> amount,
                'initialPrice' => $request -> price,
                'currentPrice' => $request -> price
            ]);
        }
    }

    public function sellItem(Request $request){
        $isAlreadyHave = Portfolio::where('name', $request -> name)->first();
        $initialPrice = $isAlreadyHave -> initialPrice;

        if($isAlreadyHave){
            // jika punya barang tersebut
            $itemLeft = $isAlreadyHave -> amount - $request -> amount;
            if($itemLeft >= 0){
                // jika barang yang mau dijual cukup

                $logs = Log::create([
                    'type' => 'sell',
                    'name' => $request -> name,
                    'amount' => $request -> amount,
                    'initialPrice' => $initialPrice,
                    'price' => $request -> price
                ]);

                $portfolio = Portfolio::where('name', $request -> name)
                ->update([
                    'amount' => $itemLeft 
                ]);
                if($itemLeft == 0){
                    Portfolio::where('name', $request -> name)->delete();
                }
                
            }else{
                // jika barang yang mau dijual kurang
            }
        }else{
            // jika tidak punya barang tersebut
        }
    }

    public function getAllPortfolios()
    {
        $portfolios = Portfolio::all();

        return response()->json($portfolios, 200);
    }

    public function changeCurrentPrice(Request $request)
    {
        $portfolio = Portfolio::where('id', $request -> id)
        ->update([
            'currentPrice' => $request -> currentPrice
        ]);

    }

    public function getStatusNow()
    {
        $portfolios = Portfolio::all();

        $statusNow['invested'] = 0;
        $statusNow['totalNow'] = 0;
        $statusNow['gainLoss'] = 0;

        foreach($portfolios as $portfolio){
            // pengkalkulasian berdasarkan item yang dimiliki
            $totalInvested = $portfolio -> amount * $portfolio -> initialPrice;
            $totalNow = $portfolio -> amount * $portfolio -> currentPrice;
            $gainLoss = $totalNow - $totalInvested;

            $statusNow['invested'] += $totalInvested;
            $statusNow['gainLoss'] += $gainLoss;
            $statusNow['totalNow'] += $totalNow;
        }
        $statusNow['gainLossPercent'] = $statusNow['gainLoss']/$statusNow['invested']*100;
        return response()->json($statusNow, 200);
    }
}
