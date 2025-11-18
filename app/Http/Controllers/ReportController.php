<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from') ? Carbon::parse($request->input('from')) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->input('to')) : now()->endOfMonth();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $filters = [
            'customer' => $request->input('customer'),
            'category' => $request->input('category'),
            'product' => $request->input('product'),
        ];

        $salesQuery = Sale::query()
            ->whereBetween('date', [$from->clone()->startOfDay(), $to->clone()->endOfDay()]);

        $salesQuery = $this->applyFilters($salesQuery, $filters);

        $totalRevenue = (clone $salesQuery)->sum('total_amount');
        $ordersCount = (clone $salesQuery)->count();
        $averageOrder = $ordersCount > 0 ? $totalRevenue / $ordersCount : 0;

        $chartData = (clone $salesQuery)
            ->selectRaw('DATE(date) as day, SUM(total_amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($row) => [
                'day' => $row->day,
                'total' => (float) $row->total,
            ]);

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.date', [$from->clone()->startOfDay(), $to->clone()->endOfDay()])
            ->when($filters['customer'], fn($q, $customer) => $q->where('sales.customer_id', $customer))
            ->when($filters['category'], fn($q, $category) => $q->where('products.category_id', $category))
            ->when($filters['product'], fn($q, $product) => $q->where('products.id', $product))
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as qty'), DB::raw('SUM(sale_items.total) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $sales = (clone $salesQuery)
            ->with(['customer', 'items.product'])
            ->orderByDesc('date')
            ->paginate(20)
            ->withQueryString();

        if ($request->input('export') === 'excel') {
            return Excel::download(new SalesReportExport($from, $to, $filters), 'sales-report-'.now()->format('Ymd_His').'.xlsx');
        }

        if ($request->input('export') === 'pdf') {
            $data = [
                'sales' => $sales->items(),
                'from' => $from,
                'to' => $to,
                'totalRevenue' => $totalRevenue,
                'ordersCount' => $ordersCount,
                'averageOrder' => $averageOrder,
            ];
            $pdf = Pdf::loadView('reports.pdf', $data);
            return $pdf->download('sales-report-'.now()->format('Ymd_His').'.pdf');
        }

        return view('reports.index', [
            'from' => $from,
            'to' => $to,
            'sales' => $sales,
            'chartData' => $chartData,
            'filters' => $filters,
            'totalRevenue' => $totalRevenue,
            'ordersCount' => $ordersCount,
            'averageOrder' => $averageOrder,
            'topProducts' => $topProducts,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['customer'], fn($q, $customer) => $q->where('customer_id', $customer))
            ->when($filters['category'], function ($q, $category) {
                $q->whereHas('items.product', fn($builder) => $builder->where('category_id', $category));
            })
            ->when($filters['product'], function ($q, $product) {
                $q->whereHas('items', fn($builder) => $builder->where('product_id', $product));
            });
    }
}
