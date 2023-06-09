<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CurrencyHistory;
use App\Services\CurrencyHistoryService;
use Illuminate\Support\Facades\Auth;

class HomeFilterRequest extends FormRequest
{

    /**
     * The CurrencyHistoryService instance.
     *
     * @var CurrencyHistoryService
     */
    private $currencyHistoryService;

    /**
     * Create a new command instance.
     *
     * @param CurrencyHistoryService $currencyHistoryService
     */
    public function __construct(
        CurrencyHistoryService $currencyHistoryService
    ) {
        parent::__construct();
        $this->currencyHistoryService = $currencyHistoryService;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $dayCurrencies = $this->currencyHistoryService->getUniqueCurrenciesId();

        $rules = [];

        if ($this->has('dateRange')) {
            $rules['dateRange'] = 'required|integer|min:0';
        }

        if ($this->has('currencyId')) {
            $rules['currencyId'] = 'required|in:' . implode(',', $dayCurrencies);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'dateRange.required' => 'The date range field is required.',
            'dateRange.integer' => 'The date range must be an integer.',
            'dateRange.min' => 'The date range must be at least 1.',
            'currencyId.required' => 'The currency ID field is required.',
            'currencyId.in' => 'The selected currency ID is invalid.',
        ];
    }
}