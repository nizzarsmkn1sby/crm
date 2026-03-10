<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Company;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['company', 'assignedUser']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('company_id')) $query->where('company_id', $request->company_id);

        $contacts = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $companies = Company::orderBy('name')->get();
        $users = User::where('is_active', true)->get();

        return view('contacts.index', compact('contacts', 'companies', 'users'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $leads = Lead::orderBy('name')->get();
        $users = User::where('is_active', true)->get();
        return view('contacts.create', compact('companies', 'leads', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'lead_id' => 'nullable|exists:leads,id',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
            'tags' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $contact = Contact::create($validated);
        return redirect()->route('contacts.show', $contact)
            ->with('success', "Kontak {$contact->name} berhasil ditambahkan!");
    }

    public function show(Contact $contact)
    {
        $contact->load(['company', 'lead', 'assignedUser', 'activities.user', 'tasks', 'deals', 'documents']);
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $companies = Company::orderBy('name')->get();
        $leads = Lead::orderBy('name')->get();
        $users = User::where('is_active', true)->get();
        return view('contacts.edit', compact('contact', 'companies', 'leads', 'users'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'lead_id' => 'nullable|exists:leads,id',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $contact->update($validated);
        return redirect()->route('contacts.show', $contact)
            ->with('success', "Kontak {$contact->name} berhasil diperbarui!");
    }

    public function destroy(Contact $contact)
    {
        $name = $contact->name;
        $contact->delete();
        return redirect()->route('contacts.index')
            ->with('success', "Kontak {$name} berhasil dihapus!");
    }
}
