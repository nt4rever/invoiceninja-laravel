<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Http\Controllers\Reports;

use App\Export\CSV\InvoiceItemExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Report\GenericReportRequest;
use App\Utils\Traits\MakesHash;
use Illuminate\Http\Response;

class InvoiceItemReportController extends BaseController
{
    use MakesHash;

    private string $filename = 'invoice_items.csv';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @OA\Post(
     *      path="/api/v1/reports/invoice_items",
     *      operationId="getInvoiceItemReport",
     *      tags={"reports"},
     *      summary="Invoice item reports",
     *      description="Export invoice item reports",
     *      @OA\Parameter(ref="#/components/parameters/X-Api-Secret"),
     *      @OA\Parameter(ref="#/components/parameters/X-Requested-With"),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/GenericReportSchema")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\Header(header="X-MINIMUM-CLIENT-VERSION", ref="#/components/headers/X-MINIMUM-CLIENT-VERSION"),
     *          @OA\Header(header="X-RateLimit-Remaining", ref="#/components/headers/X-RateLimit-Remaining"),
     *          @OA\Header(header="X-RateLimit-Limit", ref="#/components/headers/X-RateLimit-Limit"),
     *       ),
     *       @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(ref="#/components/schemas/ValidationError"),
     *       ),
     *       @OA\Response(
     *           response="default",
     *           description="Unexpected Error",
     *           @OA\JsonContent(ref="#/components/schemas/Error"),
     *       ),
     *     )
     */
    public function __invoke(GenericReportRequest $request)
    {
        // expect a list of visible fields, or use the default

        $export = new InvoiceItemExport(auth()->user()->company(), $request->all());

        $csv = $export->run();

        $headers = array(
            'Content-Disposition' => 'attachment',
            'Content-Type' => 'text/csv',
        );

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $this->filename, $headers);
        
    }



}