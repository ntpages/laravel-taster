<?php

namespace Ntpages\LaravelTaster\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;

use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Events\Interact;
use Ntpages\LaravelTaster\Models\Variant;

class DefaultController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Interaction $interaction
     * @param Variant $variant
     * @return mixed
     */
    public function interact(Interaction $interaction, Variant $variant)
    {
        event(new Interact($interaction, $variant));

        return response()->noContent();
    }
}
