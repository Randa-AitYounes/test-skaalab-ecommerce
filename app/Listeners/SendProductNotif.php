<?php

namespace App\Listeners;

use App\Events\CreateUpdateProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendProductNotif
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CreateUpdateProduct $event): void
    {
        Log::info('Produit ' . ($event->product->wasRecentlyCreated ? 'créé' : 'mis à jour') . ': ' . $event->product->name);
    }
}
