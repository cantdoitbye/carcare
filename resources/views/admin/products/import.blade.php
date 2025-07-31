@extends('admin.layouts.app')

@section('title', 'Import Products')
@section('page-title', 'Import Products')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Bulk Import Products</h5>
            </div>
            <div class="card-body">
                @if(session('import_results'))
                    @php $results = session('import_results'); @endphp
                    <div class="alert alert-info">
                        <h6>Import Results:</h6>
                        <ul class="mb-0">
                            <li>{{ $results['imported'] }} products imported</li>
                            <li>{{ $results['updated'] }} products updated</li>
                            @if(count($results['errors']) > 0)
                                <li>{{ count($results['errors']) }} errors occurred</li>
                            @endif
                        </ul>
                        
                        @if(count($results['errors']) > 0)
                            <hr>
                            <h6>Errors:</h6>
                            <ul class="mb-0">
                                @foreach($results['errors'] as $error)
                                    <li class="text-danger">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <form action="{{ route('admin.products.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Select CSV File *</label>
                        <input type="file" class="form-control @error('csv_file') is-invalid @enderror" 
                               name="csv_file" accept=".csv,.txt" required>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Maximum file size: 10MB. Supported formats: CSV
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes:</h6>
                        <ul class="mb-0">
                            <li>Products with existing SKUs will be updated</li>
                            <li>New products will be created with provided data</li>
                            <li>Categories should be referenced by their slug</li>
                            <li>Images are not imported via CSV</li>
                            <li>Make sure your CSV follows the template format</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.import.template') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>Download Template
                        </a>
                        <div>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Import Products
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- CSV Format Guide -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">CSV Format Guide</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Column</th>
                                <th>Required</th>
                                <th>Description</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>name</code></td>
                                <td><span class="badge bg-danger">Yes</span></td>
                                <td>Product name</td>
                                <td>Premium Car Wash Kit</td>
                            </tr>
                            <tr>
                                <td><code>sku</code></td>
                                <td><span class="badge bg-danger">Yes</span></td>
                                <td>Unique product identifier</td>
                                <td>CWK-001</td>
                            </tr>
                            <tr>
                                <td><code>price</code></td>
                                <td><span class="badge bg-danger">Yes</span></td>
                                <td>Product price (decimal)</td>
                                <td>29.99</td>
                            </tr>
                            <tr>
                                <td><code>quantity</code></td>
                                <td><span class="badge bg-danger">Yes</span></td>
                                <td>Stock quantity (integer)</td>
                                <td>100</td>
                            </tr>
                            <tr>
                                <td><code>brand</code></td>
                                <td><span class="badge bg-secondary">No</span></td>
                                <td>Product brand</td>
                                <td>CarCare Pro</td>
                            </tr>
                            <tr>
                                <td><code>compare_price</code></td>
                                <td><span class="badge bg-secondary">No</span></td>
                                <td>Original price for discounts</td>
                                <td>39.99</td>
                            </tr>
                            <tr>
                                <td><code>weight</code></td>
                                <td><span class="badge bg-secondary">No</span></td>
                                <td>Weight in kg</td>
                                <td>2.5</td>
                            </tr>
                            <tr>
                                <td><code>status</code></td>
                                <td><span class="badge bg-secondary">No</span></td>
                                <td>active, inactive, or draft</td>
                                <td>active</td>
                            </tr>
                            <tr>
                                <td><code>categories</code></td>
                                <td><span class="badge bg-secondary">No</span></td>
                                <td>Category slugs (comma-separated)</td>
                                <td>car-wash,interior-care</td>
                            </tr>
                            <tr>
                                <td><code>tags</code></td>
                                <td><span class="badge bg-secondary">No</span></td>
                                <td>Product tags (comma-separated)</td>
                                <td>premium,professional</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection