<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Company;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return view('search.results', [
                'q'        => $q,
                'leads'    => collect(),
                'contacts' => collect(),
                'deals'    => collect(),
                'companies'=> collect(),
                'total'    => 0,
            ]);
        }

        $like = "%{$q}%";

        $leads = Lead::with(['assignedUser', 'pipelineStage'])
            ->where(fn($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->orWhere('company', 'like', $like)
            )
            ->take(10)
            ->get();

        $contacts = Contact::with(['lead'])
            ->where(fn($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like)
            )
            ->take(10)
            ->get();

        $deals = Deal::with(['lead', 'assignedUser'])
            ->where(fn($query) => $query
                ->where('title', 'like', $like)
                ->orWhere('description', 'like', $like)
            )
            ->take(10)
            ->get();

        $companies = Company::where(fn($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('industry', 'like', $like)
                ->orWhere('website', 'like', $like)
            )
            ->take(10)
            ->get();

        $total = $leads->count() + $contacts->count() + $deals->count() + $companies->count();

        return view('search.results', compact('q', 'leads', 'contacts', 'deals', 'companies', 'total'));
    }

    /**
     * AJAX quick search — returns JSON for live dropdown
     */
    public function quick(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $like    = "%{$q}%";
        $results = [];

        Lead::where('name', 'like', $like)
            ->orWhere('company', 'like', $like)
            ->take(5)->get()
            ->each(fn($lead) => $results[] = [
                'type'  => 'lead',
                'icon'  => 'bi-person-plus-fill',
                'color' => 'text-indigo-400',
                'label' => $lead->name,
                'sub'   => $lead->company ?: $lead->email,
                'url'   => route('leads.show', $lead),
            ]);

        Contact::where('name', 'like', $like)
            ->orWhere('email', 'like', $like)
            ->take(5)->get()
            ->each(fn($c) => $results[] = [
                'type'  => 'contact',
                'icon'  => 'bi-people-fill',
                'color' => 'text-blue-400',
                'label' => $c->name,
                'sub'   => $c->email,
                'url'   => route('contacts.show', $c),
            ]);

        Deal::where('title', 'like', $like)
            ->take(3)->get()
            ->each(fn($d) => $results[] = [
                'type'  => 'deal',
                'icon'  => 'bi-bag-check-fill',
                'color' => 'text-green-400',
                'label' => $d->title,
                'sub'   => 'Rp' . number_format($d->value / 1000000, 1) . 'M',
                'url'   => route('deals.show', $d),
            ]);

        return response()->json($results);
    }
}
