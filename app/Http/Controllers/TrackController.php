<?php

namespace App\Http\Controllers;

use App\Models\TrackedAccount;
use App\Models\TrustedResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackController extends Controller
{
    public function list(Request $request): View
    {
        $trackedAccounts = $request->user()->trackedAccounts;

        return view('profile.edit', [
            'user' => $request->user(),
            'trackedAccounts' => $trackedAccounts,
        ]);
    }

    public function showAddForm(Request $request): View
    {
        return view('track.edit');
    }

    public function showEditForm(TrackedAccount $trackedAccount): View
    {
        return view('track.edit', [
            'trackedAccount' => $trackedAccount,
        ]);
    }

    /**
     * Save or update a tracked account.
     */
    public function save(Request $request, ?TrackedAccount $trackedAccount = null): RedirectResponse
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'bot_id' => ['required', 'string'],
            'account_id' => ['required', 'string'],
            'account_name' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'trusted_resources' => ['nullable', 'array'],
            'trusted_resources.*.resource_id' => ['required', 'string'],
            'trusted_resources.*.resource_name' => ['required', 'string'],
        ]);

        $data = [
            'service_id' => $request->service_id,
            'bot_id' => $request->bot_id,
            'account_id' => $request->account_id,
            'account_name' => $request->account_name,
            'notes' => $request->notes,
        ];

        if ($trackedAccount) {
            $trackedAccount->update($data);
            $message = 'tracked-account-updated';
        } else {
            $data['user_id'] = $request->user()->id;
            $data['notification_user_id'] = $request->user()->id;
            $trackedAccount = TrackedAccount::create($data);
            $message = 'tracked-account-added';
        }

        if ($request->has('trusted_resources')) {
            $existingResources = $trackedAccount->trustedResources->pluck('id')->toArray();
            $updatedResources = [];

            foreach ($request->trusted_resources as $resource) {
                $trustedResource = TrustedResource::firstOrCreate(['id' => $resource['id']]);
                $trustedResource->fill([
                    'user_id' => $request->user()->id,
                    'service_id' => $request->service_id,
                    'resource_id' => $resource['resource_id'],
                    'resource_name' => $resource['resource_name'],
                    'tracked_account_id' => $trackedAccount->id,
                ]);
                $trustedResource->save();
                $updatedResources[] = $trustedResource->id;
            }

            // Delete resources that were removed.
            $resourcesToDelete = array_diff($existingResources, $updatedResources);
            TrustedResource::whereIn('id', $resourcesToDelete)->delete();
        }

        return back()->with('status', $message);
    }
}
