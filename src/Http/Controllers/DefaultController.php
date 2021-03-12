<?php

namespace Ntpages\LaravelTaster\Http\Controllers;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Ntpages\LaravelTaster\Services\TasterService;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Events\Interact;
use Ntpages\LaravelTaster\Models\Variant;

class DefaultController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(EncryptCookies::class);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function interact(Request $request)
    {
        abort_unless($request->filled('token'), Response::HTTP_BAD_REQUEST);

        $ids = decrypt($request->get('token'));

        abort_unless(is_int($ids['interaction']) && is_int($ids['variant']), Response::HTTP_BAD_REQUEST);

        /** @var TasterService $taster */
        $taster = app('taster');

        Interact::dispatch(
            Interaction::findOrFail($ids['interaction']),
            Variant::findOrFail($ids['variant']),
            $taster->getUuid(),
            $request->headers->get('referer')
        );

        return response(true);
    }
}
