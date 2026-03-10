<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::withCount(['contacts', 'deals']);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('industry', 'like', "%{$s}%");
            });
        }
        $companies = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string',
            'website' => 'nullable|url',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'employees' => 'nullable|integer',
            'annual_revenue' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $company = Company::create($validated);
        return redirect()->route('companies.show', $company)->with('success', "Perusahaan {$company->name} berhasil ditambahkan!");
    }

    public function show(Company $company)
    {
        $company->load(['contacts', 'deals.pipelineStage']);
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string',
            'website' => 'nullable|url',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'employees' => 'nullable|integer',
            'annual_revenue' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $company->update($validated);
        return redirect()->route('companies.show', $company)->with('success', "Perusahaan berhasil diperbarui!");
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Perusahaan berhasil dihapus!');
    }
}
