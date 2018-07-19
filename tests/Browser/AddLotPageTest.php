<?php

namespace Tests\Browser;


use App\Entity\Currency;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AddLotPageTest extends DuskTestCase
{
    use RefreshDatabase;

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
        $this->actingAs($this->user)->browse(function(Browser $browser){
           $browser->visit($this->endpoint)
               ->assertSee('Add')
               ->value('input[name=price]',100)
               ->value('input[name=date_time_open]','2018/12/01 12:00:00')
               ->value('input[name=date_time_close]','2018/12/01 12:00:59')
               ->value('input[name=currency_id]',$this->currency->id)
               ->press('Add')
               ->assertSee('Lot has been added successfully!');
        });
    }
}