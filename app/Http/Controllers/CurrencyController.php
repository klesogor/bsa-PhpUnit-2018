<?php

namespace App\Http\Controllers;

use App\Presenters\LotResponsePresenter;
use App\Request\AddLotRequest;
use App\Request\BuyLotRequest;
use App\Service\Contracts\IMarketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    private $marketService;
    public function __construct(IMarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    public function addLot(Request $request)
    {
        if(!Auth::check()){
            abort(403);
        }
        $lot = $this->marketService->addLot(new AddLotRequest(
            $request->currency_id,
            Auth::id(),
            $request->date_time_open,
            $request->date_time_close,
            $request->price
        ));
        return response($lot,201);//abort(201) is fine, however this thing is here for debug
    }

    public function buyFromLot(Request $request)
    {
        if(!Auth::check()){
            abort(403);
        }
        $trade = $this->marketService->buyLot(new BuyLotRequest(
            Auth::id(),
            $request->lot_id,
            $request->amount
        ));
        return response($trade,201);
    }

    public function getLot(int $id)
    {
        return response(LotResponsePresenter::presentLotResponse($this->marketService->getLot($id)));
    }

    public function getAllLots()
    {
        return response(LotResponsePresenter::presentLotResponseArray($this->marketService->getLotList()));
    }

    public function addLotView()
    {
        return view('addLot');
    }

    public function addLotFromView(\App\Http\Requests\AddLotRequest $request)
    {
        try {
            if (!Auth::check()) {
                abort(403);
            }
            $lot = $this->marketService->addLot(new AddLotRequest(
                $request->currency_id,
                Auth::id(),
                Carbon::createFromFormat('Y/m/d h:i:s',$request->date_time_open)->getTimestamp(),
                Carbon::createFromFormat('Y/m/d h:i:s',$request->date_time_close)->getTimestamp(),
                $request->price
            ));
            return view('addLot')->with('lot', $lot);
        } catch( \Exception $exception) {
            return view('addLot')->with('error', $exception->getMessage());
        }
    }
}
