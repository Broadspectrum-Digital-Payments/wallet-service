<?php

declare(strict_types=1);

namespace App\Exports\Action;

use App\Exports\LoansExport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

final class LoansReportAction
{
    public function handle(Request $request)
    {
        try {
            $response = $this->getLoansRequest('/v1/loans', $this->makeFilterParams($request));

            info("Get Loan response", $response->json());

            if ($response->successful() && ($result = (object)$response->json())?->success) {

                return Excel::download(
                    new LoansExport($result->data),
                    'loans.csv',
                    \Maatwebsite\Excel\Excel::CSV,
                    [
                        'Content-Type' => 'text/csv',
                    ]
                );
            }

            return errorResponse("Requested data not found.", Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            report($exception);
        }

        return errorResponse("Requested data not found.", Response::HTTP_NOT_FOUND);
    }

    private function makeFilterParams(Request $request): array
    {
        $params = ['pageSize' => $request->query('pageSize', 500)];

        $feilds = ['externalId', 'startDate', 'endDate', 'status'];

        foreach ($feilds as $value) {
            if ($request->filled($value)) {
                $params[$value] = $request->query($value);
            }
        }

        return $params;
    }

    public function getLoansRequest(
        string $path,
        mixed $query = null,
        string $method = 'get',
        array $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]
    ) {
        return Http::withHeaders($headers)
            ->{$method}(env('LOAN_BASE_URL') . $path, $query);
    }
}
