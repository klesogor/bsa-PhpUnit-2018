<?php

namespace App\Service;


use App\Entity\Lot;
use App\Entity\Trade;
use App\Exceptions\MarketException\ActiveLotExistsException;
use App\Exceptions\MarketException\BuyInactiveLotException;
use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\BuyOwnCurrencyException;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Exceptions\MarketException\LotDoesNotExistException;
use App\Repository\Contracts\ICurrencyRepository;
use App\Repository\Contracts\ILotRepository;
use App\Repository\Contracts\IMoneyRepository;
use App\Repository\Contracts\ITradeRepository;
use App\Repository\Contracts\IUserRepository;
use App\Repository\Contracts\IWalletRepository;
use App\Request\Contracts\IAddLotRequest;
use App\Request\Contracts\IBuyLotRequest;
use App\Request\MoneyRequest;
use App\Response\Contracts\ILotResponse;
use App\Response\LotResponse;
use App\Service\Contracts\IMarketService;
use App\Service\Contracts\INotificationService;
use App\Service\Contracts\IWalletService;
use App\Service\Validators\Contracts\IMarketValidator;
use Carbon\Carbon;

class MarketService implements IMarketService
{

    private $lotRepository;
    private $walletRepository;
    private $tradeRepository;
    private $validator;
    private $walletService;
    private $userRepository;
    private $currencyRepository;
    private $moneyRepository;
    private $notificationService;

    public function __construct(ITradeRepository $tradeRepo,
                                ILotRepository $lotRepo,
                                IWalletRepository $walletRepo,
                                IWalletService $walletService,
                                IMarketValidator $validator,
                                IUserRepository $userRepo,
                                ICurrencyRepository $currencyRepo,
                                IMoneyRepository $moneyRepo,
                                INotificationService $notificationService)
    {
        $this->lotRepository = $lotRepo;
        $this->walletRepository = $walletRepo;
        $this->tradeRepository = $tradeRepo;
        $this->walletService = $walletService;
        $this->validator = $validator;
        $this->userRepository = $userRepo;
        $this->currencyRepository = $currencyRepo;
        $this->moneyRepository = $moneyRepo;
        $this->notificationService = $notificationService;
    }


    /**
     * Sell currency.
     *
     * @param IAddLotRequest $lotRequest
     *
     * @throws ActiveLotExistsException
     * @throws IncorrectTimeCloseException
     * @throws IncorrectPriceException
     *
     * @return Lot
     */
    public function addLot(IAddLotRequest $lotRequest): Lot
    {
        $this->validator->validateAddLot($lotRequest);
        $lot = new Lot();
        $lot->currency_id = $lotRequest->getCurrencyId();
        $lot->price = $lotRequest->getPrice();
        $lot->seller_id = $lotRequest->getSellerId();
        $lot->date_time_open = $lotRequest->getDateTimeOpen();
        $lot->date_time_close = $lotRequest->getDateTimeClose();
        return $this->lotRepository->add($lot);
    }

    /**
     * Buy currency.
     *
     * @param IBuyLotRequest $lotRequest
     *
     * @throws BuyOwnCurrencyException
     * @throws IncorrectLotAmountException
     * @throws BuyNegativeAmountException
     * @throws BuyInactiveLotException
     *
     * @return Trade
     */
    public function buyLot(IBuyLotRequest $lotRequest): Trade
    {
        $lot = $this->lotRepository->getByIdActive($lotRequest->getLotId());
        $this->validator->validateBuyLot($lotRequest,$lot);
        $this->walletService->takeMoney(new MoneyRequest(
            $this->walletRepository->findByUser($lot->seller_id)->id,
            $lot->currency_id,
            $lotRequest->getAmount()));

        $this->walletService->addMoney(new MoneyRequest(
            $this->walletRepository->findByUser($lotRequest->getUserId())->id,
            $lot->currency_id,
            $lotRequest->getAmount()));

        $trade = new Trade();

        $trade->lot_id = $lotRequest->getLotId();
        $trade->user_id = $lotRequest->getUserId();
        $trade->amount = $lotRequest->getAmount();

        $trade = $this->tradeRepository->add($trade);
        $this->notificationService->notifyTradeCreated(
            $this->userRepository->getById($lot->seller_id),
            $this->userRepository->getById($trade->user_id),
            $trade
        );
        return $trade;
    }

    /**
     * Retrieves lot by an identifier and returns it in LotResponse format
     *
     * @param int $id
     *
     * @throws LotDoesNotExistException
     *
     * @return ILotResponse
     */
    public function getLot(int $id): ILotResponse
    {
        $lot = $this->lotRepository->getById($id);
        $this->validator->validateGetLot($lot);
        return $this->lotResponseFromLot($lot);
    }

    /**
     * Return list of lots.
     *
     * @return ILotResponse[]
     */
    public function getLotList(): array
    {
        return array_map(function($lot){
            return $this->lotResponseFromLot($lot);
        },$this->lotRepository->findAll());
    }

    private function lotResponseFromLot(Lot $lot): ILotResponse
    {
        $user = $this->userRepository->getById($lot->seller_id);
        $currency = $this->currencyRepository->getById($lot->currency_id);
        return new LotResponse(
            $lot->id,
            $user->name,
            $currency->name,
            $this->moneyRepository->findByWalletAndCurrency($this->walletRepository
                ->findByUser($user->id)->id,$currency->id)->amount,
            Carbon::createFromTimestamp($lot->date_time_open)->format('Y/m/d h:i:s'),
            Carbon::createFromTimestamp($lot->date_time_close)->format('Y/m/d h:i:s'),
            number_format($lot->price,2,',', ''));
    }
}