<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Media_option;
use App\Models\Blog;
use App\Models\Blog_category;

class PackageController extends Controller
{

    public function index()
    {
        $packages = Package::all();
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'items' => 'required|integer|min:1',
            'base_monthly_price' => 'required|numeric|min:0',
            'quarterly_discount' => 'nullable|numeric|min:0|max:100',
            'bi_annual_discount' => 'nullable|numeric|min:0|max:100',
            'annual_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        Package::create($request->all());

        return redirect()->route('packages.index')->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string',
            'items' => 'required|integer|min:1',
            'base_monthly_price' => 'required|numeric|min:0',
            'quarterly_discount' => 'nullable|numeric|min:0|max:100',
            'bi_annual_discount' => 'nullable|numeric|min:0|max:100',
            'annual_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $package->update($request->all());

        return redirect()->route('packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted successfully.');
    }

}
