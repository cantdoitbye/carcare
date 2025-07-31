<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function __construct(
        protected CouponRepositoryInterface $couponRepository,
        protected ProductRepositoryInterface $productRepository,
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $coupons = match($status) {
            'active' => $this->couponRepository->getActive(),
            'expired' => $this->couponRepository->getExpired(),
            'expiring' => $this->couponRepository->getExpiringSoon(),
            default => $this->couponRepository->all()
        };

        return view('admin.coupons.index', compact('coupons', 'status'));
    }

    public function create()
    {
        $products = $this->productRepository->getActive();
        $categories = $this->categoryRepository->getActive();
        
        return view('admin.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date|after_or_equal:today',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:categories,id',
            'excluded_products' => 'nullable|array',
            'excluded_products.*' => 'exists:products,id',
            'excluded_categories' => 'nullable|array',
            'excluded_categories.*' => 'exists:categories,id',
            'status' => 'required|in:active,inactive',
            'is_public' => 'boolean',
            'free_shipping' => 'boolean',
            'exclude_sale_items' => 'boolean',
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($data['code']);
        $data['created_by'] = Auth::guard('admin')->id();

        // Convert boolean checkboxes
        $data['is_public'] = $request->boolean('is_public');
        $data['free_shipping'] = $request->boolean('free_shipping');
        $data['exclude_sale_items'] = $request->boolean('exclude_sale_items');

        $this->couponRepository->create($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function show($id)
    {
        $coupon = $this->couponRepository->find($id);
        $statistics = $this->couponRepository->getUsageStatistics($id);
        
        return view('admin.coupons.show', compact('coupon', 'statistics'));
    }

    public function edit($id)
    {
        $coupon = $this->couponRepository->find($id);
        $products = $this->productRepository->getActive();
        $categories = $this->categoryRepository->getActive();
        
        return view('admin.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons,code,' . $id,
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:categories,id',
            'excluded_products' => 'nullable|array',
            'excluded_products.*' => 'exists:products,id',
            'excluded_categories' => 'nullable|array',
            'excluded_categories.*' => 'exists:categories,id',
            'status' => 'required|in:active,inactive,expired',
            'is_public' => 'boolean',
            'free_shipping' => 'boolean',
            'exclude_sale_items' => 'boolean',
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($data['code']);

        // Convert boolean checkboxes
        $data['is_public'] = $request->boolean('is_public');
        $data['free_shipping'] = $request->boolean('free_shipping');
        $data['exclude_sale_items'] = $request->boolean('exclude_sale_items');

        $this->couponRepository->update($id, $data);

        return redirect()->route('admin.coupons.edit', $id)
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy($id)
    {
        $this->couponRepository->delete($id);
        
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate,expire',
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:coupons,id',
        ]);

        $ids = $request->selected_ids;

        switch ($request->action) {
            case 'delete':
                foreach ($ids as $id) {
                    $this->couponRepository->delete($id);
                }
                $message = 'Selected coupons deleted successfully.';
                break;

            case 'activate':
                $this->couponRepository->bulkUpdateStatus($ids, 'active');
                $message = 'Selected coupons activated successfully.';
                break;

            case 'deactivate':
                $this->couponRepository->bulkUpdateStatus($ids, 'inactive');
                $message = 'Selected coupons deactivated successfully.';
                break;

            case 'expire':
                $this->couponRepository->bulkUpdateStatus($ids, 'expired');
                $message = 'Selected coupons expired successfully.';
                break;
        }

        return redirect()->route('admin.coupons.index')->with('success', $message);
    }

    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while ($this->couponRepository->findByCode($code));

        return response()->json(['code' => $code]);
    }

    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $result = $this->couponRepository->validateCoupon($request->code);
        
        return response()->json($result);
    }

    public function duplicate($id)
    {
        $originalCoupon = $this->couponRepository->find($id);
        
        $newData = $originalCoupon->toArray();
        unset($newData['id'], $newData['created_at'], $newData['updated_at']);
        
        // Generate new code
        $newData['code'] = $originalCoupon->code . '-COPY';
        $newData['name'] = $originalCoupon->name . ' (Copy)';
        $newData['used_count'] = 0;
        $newData['status'] = 'inactive';
        $newData['created_by'] = Auth::guard('admin')->id();

        $newCoupon = $this->couponRepository->create($newData);

        return redirect()->route('admin.coupons.edit', $newCoupon->id)
            ->with('success', 'Coupon duplicated successfully. Please review and activate.');
    }

    public function export(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $coupons = match($status) {
            'active' => $this->couponRepository->getActive(),
            'expired' => $this->couponRepository->getExpired(),
            default => $this->couponRepository->all()
        };

        $filename = 'coupons_' . $status . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($coupons) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Code', 'Name', 'Type', 'Value', 'Minimum Amount', 'Usage Limit', 
                'Used Count', 'Starts At', 'Expires At', 'Status', 'Created At'
            ]);

            // CSV data
            foreach ($coupons as $coupon) {
                fputcsv($file, [
                    $coupon->id,
                    $coupon->code,
                    $coupon->name,
                    ucfirst($coupon->type),
                    $coupon->formatted_value,
                    $coupon->minimum_amount ? '$' . number_format($coupon->minimum_amount, 2) : '',
                    $coupon->usage_limit ?? 'Unlimited',
                    $coupon->used_count,
                    $coupon->starts_at ? $coupon->starts_at->format('Y-m-d H:i:s') : '',
                    $coupon->expires_at ? $coupon->expires_at->format('Y-m-d H:i:s') : '',
                    ucfirst($coupon->status),
                    $coupon->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}