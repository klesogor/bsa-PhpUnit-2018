<?php

namespace Tests\Unit;


use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Money;
use App\Entity\Wallet;
use App\Exceptions\MarketException\ActiveLotExistsException;
use App\Exceptions\MarketException\BuyInactiveLotException;
use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\BuyOwnCurrencyException;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Repository\Contracts\ICurrencyRepository;
use App\Repository\Contracts\ILotRepository;
use App\Repository\Contracts\IMoneyRepository;
use App\Repository\Contracts\ITradeRepository;
use App\Repository\Contracts\IUserRepository;
use App\Repository\Contracts\IWalletRepository;
use App\Request\AddLotRequest;
use App\Request\BuyLotRequest;
use App\Service\MarketService;
use App\Service\Validators\MarketValidator;
use App\Service\Validators\WalletValidator;
use App\Service\WalletService;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class MarketServiceTest extends TestCase
{
    private $tradeRepo;
    private $lotRepo;
    private $walletRepo;
    private $walletService;
    private $marketValidator;
    private $userRepo;
    private $currencyRepo;
    private $moneyRepo;

    private $marketService;

    protected function setUp()
    {
        parent::setUp();

        $this->tradeRepo = $this->createMock(ITradeRepository::class);
        $this->lotRepo = $this->createMock(ILotRepository::class);
        $this->walletRepo = $this->createMock(IWalletRepository::class);
        $this->userRepo= $this->createMock(IUserRepository::class);
        $this->currencyRepo = $this->createMock(ICurrencyRepository::class);
        $this->moneyRepo = $this->createMock(IMoneyRepository::class);
        $this->walletService = new WalletService(
            $this->walletRepo,
            $this->moneyRepo,
            new WalletValidator($this->moneyRepo,$this->walletRepo));
        $this->marketValidator = new MarketValidator($this->lotRepo);

        $this->marketService = new MarketService(
            $this->tradeRepo,
            $this->lotRepo,
            $this->walletRepo,
            $this->walletService,
            $this->marketValidator,
            $this->userRepo,
            $this->currencyRepo,
            $this->moneyRepo);
    }

    public function test_load()//test if bootstrap ok
    {
        $this->assertTrue(true);
    }

    public function test_add_lot_valid()
    {
        $this->lotRepo
            ->method('add')
            ->will($this->returnArgument(0));
        $timestamp = Carbon::now()->timestamp;
        $lotRequest = new AddLotRequest(
            1,
            2,
            $timestamp,
            Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp,
            100);
        $lot = $this->marketService->addLot($lotRequest);
        $this->assertEquals(1,$lot->currency_id);
        $this->assertEquals(2,$lot->seller_id);
        $this->assertEquals($lot->date_time_open,$timestamp);
        $this->assertEquals($lot->date_time_close, Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp);
        $this->assertEquals($lot->price,100);
    }

    public function test_add_lot_with_active()
    {
        $this->expectException(ActiveLotExistsException::class);

        $this->lotRepo
            ->method('findActiveLot')
            ->willReturn(new Lot);

        $timestamp = Carbon::now()->timestamp;

        $lotRequest = new AddLotRequest(
            1,
            2,
            $timestamp,
            Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp,
            100);

        $lot = $this->marketService->addLot($lotRequest);
    }

    public function test_add_lot_negative_price()
    {
        $this->expectException(IncorrectPriceException::class);
        $timestamp = Carbon::now()->timestamp;

        $lotRequest = new AddLotRequest(
            1,
            2,
            $timestamp,
            Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp,
            -100);

        $lot = $this->marketService->addLot($lotRequest);
    }

    public function test_add_lot_incorrect_time()
    {
        $this->expectException(IncorrectTimeCloseException::class);
        $timestamp = Carbon::now()->timestamp;

        $lotRequest = new AddLotRequest(
            1,
            2,
            Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp,
            $timestamp,
            -100);

        $lot = $this->marketService->addLot($lotRequest);
    }

    public function test_buy_lot_valid()
    {
        $this->lotRepo->method('getByIdActive')->will($this->returnCallback(function($id){
            $lot = new Lot();
            $timestamp = Carbon::now()->timestamp;
            $lot->id = 1;
            $lot->currency_id = 1;
            $lot->seller_id = 2;
            $lot->date_time_open = $timestamp;
            $lot->date_time_close = Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp;
            $lot->price = 100;
            return $lot;
        }));

        $this->tradeRepo->method('add')->will($this->returnArgument(0));

        $this->moneyRepo->method('findByWalletAndCurrency')
            ->will($this->onConsecutiveCalls(
                new Money(['amount' => 200]),
                new Money(['amount' => 0]))
            );

        $this->moneyRepo->method('save')->will($this->returnArgument(0));
        $this->walletRepo->method('findByUser')->willReturn(new Wallet(['id'=>1,'currency_id'=>1]));

        $request = new BuyLotRequest(1,1,50);
        $trade = $this->marketService->buyLot($request);
        $this->assertEquals(1, $trade->user_id);
        $this->assertEquals(1, $trade->lot_id);
        $this->assertEquals(50,$trade->amount);
    }

    public function test_buy_lot_inactive()
    {
        $this->expectException(BuyInactiveLotException::class);

        $this->lotRepo->method('getByIdActive')->willReturn(null);

        $this->tradeRepo->method('add')->will($this->returnArgument(0));

        $this->moneyRepo->method('findByWalletAndCurrency')
            ->will($this->onConsecutiveCalls(
                new Money(['amount' => 200]),
                new Money(['amount' => 0]))
            );

        $this->moneyRepo->method('save')->will($this->returnArgument(0));
        $this->walletRepo->method('findByUser')->willReturn(new Wallet(['id'=>1,'currency_id'=>1]));

        $request = new BuyLotRequest(1,1,50);
        $this->marketService->buyLot($request);
    }

    public function test_buy_own_lot()
    {
        $this->expectException(BuyOwnCurrencyException::class);

        $this->lotRepo->method('getByIdActive')->will($this->returnCallback(function($id){
            $lot = new Lot();
            $timestamp = Carbon::now()->timestamp;
            $lot->id = 1;
            $lot->currency_id = 1;
            $lot->seller_id = 2;
            $lot->date_time_open = $timestamp;
            $lot->date_time_close = Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp;
            $lot->price = 100;
            return $lot;
        }));

        $this->tradeRepo->method('add')->will($this->returnArgument(0));

        $this->moneyRepo->method('findByWalletAndCurrency')
            ->will($this->onConsecutiveCalls(
                new Money(['amount' => 200]),
                new Money(['amount' => 0]))
            );

        $this->moneyRepo->method('save')->will($this->returnArgument(0));
        $this->walletRepo->method('findByUser')->willReturn(new Wallet(['id'=>1,'currency_id'=>1]));

        $request = new BuyLotRequest(2,1,50);
        $this->marketService->buyLot($request);
    }

    public function test_buy_negative_amount()
    {
        $this->expectException(BuyNegativeAmountException::class);

        $this->lotRepo->method('getByIdActive')->will($this->returnCallback(function($id){
            $lot = new Lot();
            $timestamp = Carbon::now()->timestamp;
            $lot->id = 1;
            $lot->currency_id = 1;
            $lot->seller_id = 2;
            $lot->date_time_open = $timestamp;
            $lot->date_time_close = Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp;
            $lot->price = 100;
            return $lot;
        }));

        $this->tradeRepo->method('add')->will($this->returnArgument(0));

        $this->moneyRepo->method('findByWalletAndCurrency')
            ->will($this->onConsecutiveCalls(
                new Money(['amount' => 200]),
                new Money(['amount' => 0]))
            );

        $this->moneyRepo->method('save')->will($this->returnArgument(0));
        $this->walletRepo->method('findByUser')->willReturn(new Wallet(['id'=>1,'currency_id'=>1]));

        $request = new BuyLotRequest(1,1,-5);
        $this->marketService->buyLot($request);
    }

    public function test_buy_more_than_wallet_have()
    {
        $this->expectException(IncorrectLotAmountException::class);

        $this->lotRepo->method('getByIdActive')->will($this->returnCallback(function($id){
            $lot = new Lot();
            $timestamp = Carbon::now()->timestamp;
            $lot->id = 1;
            $lot->currency_id = 1;
            $lot->seller_id = 2;
            $lot->date_time_open = $timestamp;
            $lot->date_time_close = Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp;
            $lot->price = 100;
            return $lot;
        }));

        $this->tradeRepo->method('add')->will($this->returnArgument(0));

        $this->moneyRepo->method('findByWalletAndCurrency')
            ->will($this->onConsecutiveCalls(
                new Money(['amount' => 10]),
                new Money(['amount' => 0]))
            );

        $this->moneyRepo->method('save')->will($this->returnArgument(0));
        $this->walletRepo->method('findByUser')->willReturn(new Wallet(['id'=>1,'currency_id'=>1]));

        $request = new BuyLotRequest(1,1,1000);
        $this->marketService->buyLot($request);
    }

    public function test_get_lot()
    {
        $timestamp = Carbon::now()->timestamp;
        $this->lotRepo->method('getById')->will($this->returnCallback(function($id)use($timestamp){
            $lot = new Lot();
            $lot->id = 1;
            $lot->currency_id = 1;
            $lot->seller_id = 2;
            $lot->date_time_open = $timestamp;
            $lot->date_time_close = Carbon::createFromTimestamp($timestamp)->addHour(2)->timestamp;
            $lot->price = 100;
            return $lot;
        }));

        $this->currencyRepo->method('getById')->willReturn(new Currency(['name'=>'test','id'=>1]));
        $this->userRepo->method('getById')->willReturn(new User(['name'=>'Vasya','id'=>1]));
        $this->walletRepo->method('findByUser')->willReturn(new Wallet(['id'=>1,'currency_id'=>1]));
        $this->moneyRepo->method('findByWalletAndCurrency')->willReturn(new Money(['amount'=>10]));

        $response = $this->marketService->getLot(1);
        $this->assertEquals(1,$response->getId());
        $this->assertEquals('Vasya',$response->getUserName());
        $this->assertEquals('test',$response->getCurrencyName());
        $this->assertEquals(10,$response->getAmount());
        $this->assertEquals(Carbon::createFromTimestamp($timestamp)
            ->format('Y/m/d h:i:s'),$response->getDateTimeOpen());
        $this->assertEquals(Carbon::createFromTimestamp($timestamp)
            ->addHour(2)
            ->format('Y/m/d h:i:s'),$response->getDateTimeClose());
        $this->assertEquals('100,00',$response->getPrice());
    }
}