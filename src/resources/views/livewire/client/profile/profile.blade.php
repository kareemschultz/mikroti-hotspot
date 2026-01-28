<div class="row">
    <div class="col-12">
        <x-partials.flash />
    </div>
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header card-header-standard">
                <h6 class="card-title">
                    <i class="fas fa-id-card"></i>Hotspot Profiles
                    <span class="badge badge-primary">{{ $this->profiles->total() }}</span>
                </h6>
                <div class="card-actions">
                    <a class="btn btn-action btn-action-primary" href="{{ route('client.vouchers.profile.create') }}">
                        <i class="fas fa-plus"></i>Create Profile
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Bulk Action Bar --}}
                @if(count($selectedItems) > 0)
                <div class="bulk-action-bar">
                    <span class="selected-count"><strong>{{ count($selectedItems) }}</strong> profile(s) selected</span>
                    <div class="btn-action-group">
                        <button class="btn btn-action btn-action-delete"
                            wire:confirm="Are you sure you want to delete {{ count($selectedItems) }} profile(s) and ALL their vouchers?"
                            wire:click="bulkDelete">
                            <i class="fas fa-trash"></i>Delete Selected
                        </button>
                    </div>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-standard table-striped">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" wire:model.live="selectAll" class="checkbox-standard">
                                </th>
                                <th>Profile Information</th>
                                <th>Bandwidth</th>
                                <th>Limits</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->profiles as $profile)
                                <tr>
                                    <td>
                                        <input type="checkbox" wire:model.live="selectedItems"
                                            value="{{ $profile->id }}" class="checkbox-standard">
                                    </td>
                                    <td>
                                        <span class="info-label">Name:</span> 
                                        <x-partials.profile-badge 
                                            :name="$profile->name" 
                                            :uptime-limit="$profile->uptime_limit ?? 0"
                                            :data-limit="$profile->data_limit ?? 0"
                                            :validity="$profile->validity ?? 0" /><br />
                                        <span class="info-label">Description:</span> <span class="info-value-muted">{{ $profile->description ?: 'N/A' }}</span><br />
                                        <span class="info-label">Price:</span> <x-partials.price-display :price="$profile->price" />
                                    </td>
                                    <td>
                                        <span class="info-label">Max Upload:</span>
                                        <span class="info-value">{{ $profile->max_upload >= 1 ? "{$this->convertBytes($profile->max_upload)}ps" : 'Unlimited' }}</span><br />
                                        <span class="info-label">Max Download:</span>
                                        <span class="info-value">{{ $profile->max_download >= 1 ? "{$this->convertBytes($profile->max_download)}ps" : 'Unlimited' }}</span>
                                    </td>
                                    <td>
                                        <span class="info-label">Data Limit:</span>
                                        <span class="info-value">{{ $profile->data_limit >= 1 ? "{$this->convertBytes($profile->data_limit)}" : 'Unlimited' }}</span><br />
                                        <span class="info-label">Uptime Limit:</span>
                                        <span class="info-value">{{ $profile->uptime_limit >= 1 ? "{$this->convertSeconds($profile->uptime_limit)}" : 'None' }}</span><br />
                                        <span class="info-label">Validity:</span>
                                        <span class="info-value">{{ $profile->validity >= 1 ? "{$this->convertSeconds($profile->validity)}" : 'None' }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a class="btn btn-action btn-action-edit"
                                                href="{{ route('client.vouchers.profile.edit', $profile->id) }}">
                                                <i class="fas fa-edit"></i>Edit
                                            </a>
                                            <button class="btn btn-action btn-action-delete"
                                                wire:confirm.prompt="Are you sure?\nThis will delete all vouchers with this profile\n\nType {{ $profile->id }} to confirm|{{ $profile->id }}"
                                                wire:click="deleteProfile({{ $profile->id }})">
                                                <i class="fas fa-trash"></i>Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="table-empty-state">
                                        <i class="fas fa-id-card"></i>
                                        <p>No profiles created yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    {{ $this->profiles->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
