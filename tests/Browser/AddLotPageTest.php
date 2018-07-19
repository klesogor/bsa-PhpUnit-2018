<?php

namespace Tests\Browser;


use App\Entity\Currency;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Carbon\Carbon;
use App\Entity\Lot;

class AddLotPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $endpoint = '/market/lots/add';
    private $pass = 'secret';
    private $currency;
    private $user;

    protected function setUp()
    {
       parent::setUp();

       $this->currency = factory(Currency::class)->create();
       $this->user = factory(User::class)->create();
    }

    public function test_successful_add_lot()
    {
        $this->browse(function(Browser $browser){
           $browser->loginAs($this->user)
               ->visit($this->endpoint)
               ->assertSee('Add')
               ->value('input[name=price]',100)
               ->value('input[name=date_time_open]','2018/12/01 12:00:00')
               ->value('input[name=date_time_close]','2018/12/01 12:00:59')
               ->value('input[name=currency_id]',$this->currency->id)
               ->press('Add')
               ->assertPathIs('/market/lots/add')
               ->assertSee('Lot has been added successfully!');
        });
    }

    public function test_add_lot_validation()
    {
        $this->browse(function(Browser $browser){
            $browser->loginAs($this->user)
                ->visit($this->endpoint)
                ->assertSee('Add')
                ->value('input[name=price]',-1)
                ->value('input[name=date_time_open]','20dadasd')
                ->value('input[name=date_time_close]','2ds00:59')
                ->value('input[name=currency_id]',-5)
                ->press('Add')
                ->assertSee('The price must be at least 0.')
                ->assertSee('The date time open does not match the format Y/m/d H:i:s.')
                ->assertSee('The date time close does not match the format Y/m/d H:i:s.')
                ->assertSee('The currency id must be at least 0.');
         });
    }

    public function test_add_has_active_lot()
    {
        $lot = new Lot([
            'seller_id'=>$this->user->id,
            'currency_id'=>$this->currency->id,
            'price' => 10,
            'date_time_open' => Carbon::now()->getTimestamp(),
            'date_time_close' => Carbon::now()->getTimestamp()+1000,
        ]);
        $lot->save();
        $this->browse(function(Browser $browser){
            $browser->loginAs($this->user)
                ->visit($this->endpoint)
                ->assertSee('Add')
                ->value('input[name=price]',100)
                ->value('input[name=date_time_open]','2018/12/01 12:00:00')
                ->value('input[name=date_time_close]','2018/12/01 12:00:59')
                ->value('input[name=currency_id]',$this->currency->id)
                ->press('Add')
                ->assertPathIs('/market/lots/add')
                ->assertSee('Sorry, error has been occurred: You already have active lot!');
         });
    }
}