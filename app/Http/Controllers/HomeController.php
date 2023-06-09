<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Session\Store;
use App\Http\Requests\HomeFilterRequest;
use App\Services\UserService;
use App\Services\CurrencyHistoryService;

class HomeController extends Controller
{
    /**
     * The session store instance.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * The UserService instance.
     *
     * @var UserService
     */
    private $userService;

    /**
     * The CurrencyHistoryService instance.
     *
     * @var CurrencyHistoryService
     */
    private $currencyHistoryService;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Session\Store  $session
     * @param  \App\Services\UserService  $userService
     * @param  \App\Services\CurrencyHistoryService  $currencyHistoryService
     * @return void
     */
    public function __construct(
        Store $session,
        UserService $userService,
        CurrencyHistoryService $currencyHistoryService
    ) {
        $this->middleware('auth');
        $this->session = $session;
        $this->userService = $userService;
        $this->currencyHistoryService = $currencyHistoryService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get current user
        $user = auth()->user();

        // Get start day from session or use default value (1)
        $startDate = $this->session->get('startDate', 1);

        // Get selected user currencies 
        $selectedCurrencies = $this->userService->getUserCurrencies($user);

        // Get chosen currency from session or use default value (first currency)
        $choosenID = $this->session->get('choosenID', $selectedCurrencies->first());

        // Get day currencies
        $dayCurrencies = $this->currencyHistoryService->getDayCurrencies($selectedCurrencies, $startDate);

        $lastCurrencies = $dayCurrencies->reverse()->unique('name');

        $labels = $dayCurrencies->where('id', $choosenID)->pluck('updated_at');

        $data = $dayCurrencies->where('id', $choosenID)->pluck('sell');

        $name = $dayCurrencies->where('id', $choosenID)->unique('name')->pluck('name');

        return view('home')
            ->with('dayCurrencies', $dayCurrencies)
            ->with('startDate', $startDate)
            ->with('choosenID', $choosenID)
            ->with('lastCurrencies', $lastCurrencies)
            ->with('labels', $labels)
            ->with('data', $data)
            ->with('name', $name);
    }

    public function filtered(HomeFilterRequest $request)
    {
        // Get current user
        $user = auth()->user();

        // Update start day if provided in the request
        if ($request->has('dateRange')) {
            $startDate = $request->input('dateRange');
            // Save the updated start day to session
            $this->session->put('startDate', $startDate);
        }

        // Update chosen currency if provided in the request
        if ($request->has('currencyId')) {
            $choosenID = $request->input('currencyId');
            // Save the updated chosen currency to session
            $this->session->put('choosenID', $choosenID);
        }
        return $this->index();
    }
}
