<?php

namespace Tests\Feature;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Money;
use App\Entity\Wallet;
use App\Mail\TradeCreated;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_lot_valid()
    {
        $user = factory(User::class)->create();
        factory(Wallet::class)->create(['user_id'=>$user->id]);
        $timestamp = Carbon::now()->timestamp;
        $timestampEnd = Carbon::createFromTimestamp($timestamp)->addHour()->timestamp;
        $currency = factory(Currency::class)->create();
        $response = $this->actingAs($user)
            ->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $timestamp,
                'date_time_close' => $timestampEnd,
                'price' => 10
            ]);

        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');

        $this->assertDatabaseHas('lots',[
            'seller_id' => $user->id,
            'date_time_open' => Carbon::createFromTimestamp($timestamp)->format('Y-m-d H:i:s'),
            'date_time_close' => Carbon::createFromTimestamp($timestampEnd)->format('Y-m-d H:i:s'),
            'currency_id' => $currency->id,
            'price'=>10
        ]);

        $this->assertNotNull(Lot::active()->first());

    }

    public function test_add_lot_negative_price()
    {
        $user = factory(User::class)->create();
        factory(Wallet::class)->create(['user_id'=>$user->id]);
        $timestamp = Carbon::now()->timestamp;
        $timestampEnd = Carbon::createFromTimestamp($timestamp)->addHour()->timestamp;
        $currency = factory(Currency::class)->create();
        $response = $this->actingAs($user)
            ->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $timestamp,
                'date_time_close' => $timestampEnd,
                'price' => -5
            ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);

        $response->assertJson(['error'=>[
            'message'=>'Price should be zero or greater',
            'status_code' =>400
        ]]);
    }

    public function test_add_lot_incorrect_time()
    {
        $user = factory(User::class)->create();
        factory(Wallet::class)->create(['user_id'=>$user->id]);
        $timestamp = Carbon::now()->timestamp;
        $timestampEnd = Carbon::createFromTimestamp($timestamp)->addHour()->timestamp;
        $currency = factory(Currency::class)->create();
        $response = $this->actingAs($user)
            ->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $timestampEnd,
                'date_time_close' => $timestamp,
                'price' => 1
            ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);
        $response->assertJson(['error'=>[
            'message'=>'You can\'t open lot, where close time is earlier than open time',
            'status_code' =>400
        ]]);
    }

    public function test_add_lot_active_already_exists()
    {
        $user = factory(User::class)->create();
        factory(Wallet::class)->create(['user_id'=>$user->id]);
        $timestamp = Carbon::now()->timestamp;
        $timestampEnd = Carbon::createFromTimestamp($timestamp)->addHour()->timestamp;
        $currency = factory(Currency::class)->create();

        factory(Lot::class)->create([
            'seller_id' => $user->id,
            'currency_id' => $currency->id,
            'date_time_open' => $timestamp,
            'date_time_close' => $timestampEnd
        ]);

        $response = $this->actingAs($user)
            ->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $timestamp,
                'date_time_close' => $timestampEnd,
                'price' => 1
            ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);
        $response->assertJson(['error'=>[
            'message'=>'You already have active lot!',
            'status_code' =>400
        ]]);
    }

    public function test_add_lot_unauthorized()
    {
        $response = $this->json('POST','/api/v1/lots',[
                'currency_id' => 1,
                'date_time_open' => 1,
                'date_time_close' => 1,
                'price' => 1
            ]);

        $response->assertStatus(403);
    }

    public function test_buy_lot_valid()
    {
        Mail::fake();

        Queue::fake();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $sellerWallet = factory(Wallet::class)->create(['user_id'=>$user1->id]);
        $buyerWallet = factory(Wallet::class)->create(['user_id'=>$user2->id]);
        $timestamp = Carbon::now()->timestamp;
        $timestampEnd = Carbon::createFromTimestamp($timestamp)->addHour()->timestamp;
        $currency = factory(Currency::class)->create();
        $lot = factory(Lot::class)->create([
            'seller_id' => $user1->id,
            'currency_id' => $currency->id,
            'date_time_open' => $timestamp,
            'date_time_close' => $timestampEnd,
            'price' => 10
        ]);

        factory(Money::class)->create([
            'amount'=>100,
            'currency_id'=>$currency->id,
            'wallet_id' => $sellerWallet->id
        ]);

        $response = $this->actingAs($user2)
            ->json('POST',"/api/v1/trades",
                [
                   'lot_id' => $lot->id,
                    'amount'=>20
                ]);
        $response->assertStatus(201);
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertDatabaseHas('trades',[
            'lot_id' => $lot->id,
            'amount'=>20,
            'user_id' => $user2->id
        ]);
        $this->assertDatabaseHas('money',[
            'wallet_id' => $sellerWallet->id,
            'amount' =>80
        ]);

        $this->assertDatabaseHas('money',[
            'wallet_id' => $buyerWallet->id,
            'amount' =>20
        ]);

        Mail::assertQueued(TradeCreated::class,1);
    }

    public function test_buy_inactive_lot()
    {
        Mail::fake();

        Queue::fake();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $sellerWallet = factory(Wallet::class)->create(['user_id'=>$user1->id]);
        $timestamp = Carbon::now()->timestamp;
        $currency = factory(Currency::class)->create();
        $lot = factory(Lot::class)->create([
            'seller_id' => $user1->id,
            'currency_id' => $currency->id,
            'date_time_open' => $timestamp-1000000,
            'date_time_close' => $timestamp-100000,
            'price' => 10
        ]);

        factory(Money::class)->create([
            'amount'=>100,
            'currency_id'=>$currency->id,
            'wallet_id' => $sellerWallet->id
        ]);

        $response = $this->actingAs($user2)
            ->json('POST',"/api/v1/trades",
                [
                    'lot_id' => $lot->id,
                    'amount'=>20
                ]);
        $response->assertStatus(400);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJson(['error'=>[
            'message'=>'This lot is inactive',
            'status_code' =>400
        ]]);
    }

    public function test_buy_own_lot()
    {
        Mail::fake();

        Queue::fake();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $sellerWallet = factory(Wallet::class)->create(['user_id'=>$user1->id]);
        $timestamp = Carbon::now()->timestamp;
        $timestampEnd = Carbon::createFromTimestamp($timestamp)->addHour()->timestamp;
        $currency = factory(Currency::class)->create();
        $lot = factory(Lot::class)->create([
            'seller_id' => $user1->id,
            'currency_id' => $currency->id,
            'date_time_open' => $timestamp,
            'date_time_close' => $timestampEnd,
            'price' => 10
        ]);

        factory(Money::class)->create([
            'amount'=>100,
            'currency_id'=>$currency->id,
            'wallet_id' => $sellerWallet->id
        ]);

        $response = $this->actingAs($user1)
            ->json('POST',"/api/v1/trades",
                [
                    'lot_id' => $lot->id,
                    'amount'=>20
                ]);
        $response->assertStatus(400);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJson(['error'=>[
            'message'=>'You can\'t buy currency from your own lot!',
            'status_code' =>400
        ]]);
    }

    public function test_buy_lot_too_much_currency()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $sellerWallet = factory(Wallet::class)->create(['user_id'=>$user1->id]);
        $timestamp = Carbon::now()->timestamp;
        $timestampEnd = Carbon::createFromTimestamp($timestamp)->addHour()->timestamp;
        $currency = factory(Currency::class)->create();
        $lot = factory(Lot::class)->create([
            'seller_id' => $user1->id,
            'currency_id' => $currency->id,
            'date_time_open' => $timestamp,
            'date_time_close' => $timestampEnd,
            'price' => 10
        ]);

        factory(Money::class)->create([
            'amount'=>100,
            'currency_id'=>$currency->id,
            'wallet_id' => $sellerWallet->id
        ]);

        $response = $this->actingAs($user2)
            ->json('POST',"/api/v1/trades",
                [
                    'lot_id' => $lot->id,
                    'amount'=>20000
                ]);

        $response->assertStatus(400);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJson(['error'=>[
            'message'=>'Seller doesn\'t has that much currency',
            'status_code' =>400
        ]]);
    }

    public function test_buy_lot_unauthorized()
    {

        $response = $this->json('POST',"/api/v1/trades",
                [
                    'lot_id' => 1,
                    'amount'=>20
                ]);
        $response->assertStatus(403);

    }

    public function test_get_lot()
    {
        $user1 = factory(User::class)->create();
        $sellerWallet = factory(Wallet::class)->create(['user_id'=>$user1->id]);
        $currency = factory(Currency::class)->create();
        factory(Money::class)->create([
            'amount'=>100,
            'currency_id'=>$currency->id,
            'wallet_id' => $sellerWallet->id
        ]);
        $timestamp = Carbon::now()->timestamp;
        $timestampEnd = Carbon::createFromTimestamp($timestamp)->addHour()->timestamp;

        $lot = factory(Lot::class)->create([
            'seller_id' => $user1->id,
            'currency_id' => $currency->id,
            'date_time_open' => $timestamp,
            'date_time_close' => $timestampEnd,
            'price' => 10
        ]);
        $response = $this->json('GET',"/api/v1/lots/$lot->id");
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        $this->assertJsonData(json_decode($response->getContent(),true));
    }

    public function test_multiple_lots()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $sellerWallet = factory(Wallet::class)->create(['user_id'=>$user1->id]);
        $buyerWallet = factory(Wallet::class)->create(['user_id'=>$user2->id]);
        $currency = factory(Currency::class)->create();
        factory(Money::class)->create([
            'amount'=>100,
            'currency_id'=>$currency->id,
            'wallet_id' => $sellerWallet->id
        ]);
        factory(Money::class)->create([
            'amount'=>200,
            'currency_id'=>$currency->id,
            'wallet_id' => $buyerWallet->id
        ]);
        $timestamp = Carbon::now()->timestamp;
        $timestampEnd = Carbon::createFromTimestamp($timestamp)->addHour()->timestamp;

        $lot = factory(Lot::class)->create([
            'seller_id' => $user1->id,
            'currency_id' => $currency->id,
            'date_time_open' => $timestamp,
            'date_time_close' => $timestampEnd,
            'price' => 10
        ]);
        $lot = factory(Lot::class)->create([
            'seller_id' => $user2->id,
            'currency_id' => $currency->id,
            'date_time_open' => $timestamp,
            'date_time_close' => $timestampEnd,
            'price' => 1000
        ]);
        $response = $this->json('GET',"/api/v1/lots");
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        foreach(json_decode($response->getContent(),true) as $item) {
            $this->assertJsonData($item);
        }
    }

    private function assertJsonData(array $data)
    {
        $this->assertNotNull($data['id']);
        $this->assertRegExp('/\d+,\d{2}/',$data['price']); //special format, it's not float since ',' is delim.
        $this->assertTrue(is_numeric($data['amount']));//floats with zero fractions are converted to ints
        $this->assertNotNull($data['user_name']);
        $this->assertNotNull($data['currency_name']);
        $this->assertRegExp('/\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2}/',$data['date_time_open']);
        $this->assertRegExp('/\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2}/',$data['date_time_close']);
    }

}